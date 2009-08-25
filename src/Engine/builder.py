"""
QSOS-Engine's builder module.

This module provides all required tools to build a qsos evaluation sheet from
splitted repository's qscore, qin and qtpl files
"""
##
#    @defgroup builder Builder
#    @ingroup Engine 
#    @author Hery Randriamanamihaga

from Engine import document
from Engine import family
from xml.dom import minidom
from Repository import gitshelve as git
import re
import os

##
#    @ingroup builder
#
def build(evaluation, repositoryroot):
    """
    Builds name-version's qsos evaluation document
    
    @param id
            Evaluation identifier
    @param repositoryroot
            Path to local repository's root.
    @return
        Builded Document object of name-version's qsos evaluation
    """
    
    #Unpack evaluation parameter and build base path
    (id,version)=evaluation
    base = os.path.join(repositoryroot,"sheets")
    
    #Read the header of requested evaluation from repository
    header = os.path.join(base,"evaluations", id, version, "header.qscore")
    header = "".join(line.strip() for line in file(header).readlines())
    
    #Extract template content
    content = minidom.parseString(header).firstChild
    
    #Extract properties from header contents
    properties = {}
    for node in content.firstChild.childNodes :
        try :
            properties[node.tagName] = node.firstChild.data
        except AttributeError:
            properties[node.tagName] = "N/A"
            
    #Set appname and release properties from evaluation parameter
    properties["release"]=version
    properties["qsosappname"]=id
    
    #Read includes of families from header. generic include must also be added
    families = [node.firstChild.data for node in content.lastChild.childNodes]
    includes = ["generic"]
    for f in families :
        xml = os.path.join(base, "templates", f + ".qtpl")
        xml = "".join(line.strip() for line in file(xml).readlines())
        xml = minidom.parseString(xml).firstChild
        for node in xml.childNodes :
            includes.append(node.firstChild.data)
    
    #Handle each family to be included
    tree = {}
    authors = {}
    for include in includes :
        #parse .qscore file
        relPath = os.path.join("evaluations" ,id, version, include + ".qscore")
        absPath = os.path.join(base, relPath)
        sheet = "".join(line.strip() for line in file(absPath).readlines())
        xml = minidom.parseString(sheet).firstChild
        (name,email) = getAuthor(repositoryroot, os.path.join("sheets" ,relPath))
        authors[email]=name
        #Scores and comments extraction loop
        scores = {}
        comments = {}
        for node in xml.childNodes :
            n = node.getAttribute("name")
            v = node.getElementsByTagName("score")
            if v : scores[n] = v[0].firstChild.data 
            v = node.getElementsByTagName("comment")
            if v : comments[n] = v[0].firstChild.data
            
        #Add the family into family list for the document 
        tree[include]=family.include(authors, scores, comments)
        
    #Create and return the expected documents
    return document.Document(properties,families, tree)
    
##
#    @ingroup builder
#
def assembleSheet(document, repositoryroot):
    """
    Generate the qsos XML file corresponding to given document object
    
    @param document
        Evaluation's document object.
        
    @param repositoryroot
        Path to repository's root.
    @return
        qsos XML string flow of document's content
    """
    #Create the global frame of the qsos document
    sheet = minidom.Document()
    root = sheet.createElement("document")
    header = sheet.createElement("header")
    authors = sheet.createElement("authors")
    header.appendChild(authors)
    root.appendChild(header)
    
 
    
    #Fill in header with properties
    for item in document["properties"] :
        tag = sheet.createElement(item)
        tag.appendChild(sheet.createTextNode(document["properties"][item]))
        header.appendChild(tag)
    
    appfamilies = sheet.createElement("qsosappfamilies")
    header.appendChild(appfamilies)
    
    auths = {}
    #Add blank qsos evaluation of families
    for item in document["families"] :
        app = sheet.createElement("qsosappfamily")
        app.appendChild(sheet.createTextNode(item))
        appfamilies.appendChild(app)
        
    for item in document["includes"] :
        for mail,name in document[item]['authors'].iteritems() :
            auths[name] = mail
                    
        include = os.path.join(repositoryroot,"sheets","families",item + ".qin")
        include = "".join(line.strip() for line in file(include).readlines())
        include = minidom.parseString(include).firstChild
        for section in include.childNodes[1:] : root.appendChild(section)
    
    #Fill-in authors tag with data extracted 
    for k, v in auths.iteritems() :
        tag = sheet.createElement('author')
        leaf = sheet.createElement("email")
        leaf.appendChild(sheet.createTextNode(v))
        tag.appendChild(leaf)
        leaf = sheet.createElement("name")
        leaf.appendChild(sheet.createTextNode(k.decode('utf-8')))
        tag.appendChild(leaf)
        authors.appendChild(tag)
    #Finalize the document
    sheet.appendChild(root)
    return sheet.toxml('utf-8')

##
#    @ingroup builder
#
def fillSheet(document, sheet, repositoryroot):   
    #Lambda fucntions :
    #    * add a text node in an element
    #    * check if the element has the child node and create it if not
    
    addTextNode = lambda element,tag,text : element.getElementsByTagName(tag).  \
                                item(0).appendChild(sheet.createTextNode(text))
                        
    checkChildNode = lambda element,tag :                                       \
                            element.getElementsByTagName(tag)                   \
                        or                                                      \
                            element.appendChild(sheet.createElement(tag))
                            
    sheet = minidom.parseString(sheet)
    
    #Define ID tag for the document
    for e in sheet.getElementsByTagName("element") : e.setIdAttribute("name")
    
    #Fill-in evaluations' section with families data
    for item in document["includes"] :
        scores = document[item].scores.copy()
        comments = document[item].comments.copy()
        
        #Iterate over families' contents :
        #  (Scores and comments are duplicated so destructive iteration
        #   won't affect families reference to them)
        #
        #    -Proceed first with elements with score :
        #        * Begin with fetching the element and corresponding score
        #        * Check if element has score tag (and add if so not)
        #        * Check if element has comment component and fill-in the tag
        #            (create it if doesn't exist yet)
        #    -Do the same with remaining comments

        while scores :
            (element,score) = scores.popitem()
            e =  sheet.getElementById(element)
            
            checkChildNode(e,"score")
            addTextNode(e,"score",score)
            
            if element in comments :
                checkChildNode(e,"comment")
                addTextNode(e,"comment",comments.pop(element))
                
        while comments :
            (element, comment) = comments.popitem()
            e =  sheet.getElementById(element)
            checkChildNode(e,"comment")
            addTextNode(e,"comment",comment)
            
    return sheet.toprettyxml("\t", "\n", "utf-8")


def getAuthor(repository, file):
    log = git.git('log', file)
    result = re.compile('Author: (.*)<(.+@.+)>').search(log)
    if result :
        return result.group(1), result.group(2)
    else :
        raise StandardError("No author found for " + file + " in " + repository)
    