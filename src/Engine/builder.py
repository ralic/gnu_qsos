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
def build(id, repositoryroot):
    """
    Builds name-version's qsos evaluation document
    
    @param id
            Evaluation identifier
    @param repositoryroot
            Path to local repository's root.
    @return
        Builded Document object of name-version's qsos evaluation
    """
    print "Builder invoked"
    #Read the template of requested evaluation from repository
    template = os.path.join(repositoryroot,"sheets","templates", id + ".qtpl")
    template = "".join(line.strip() for line in file(template).readlines())
    
    #Extract template content
    content = minidom.parseString(template).firstChild.lastChild.childNodes
    
    #Extract properties from template contents
    properties = dict((node.tagName,node.firstChild.data) for node in content[0:-2])
    
    #Build families object according to families declared from template
    #generic *family* must also be added to families
    includes = [node.firstChild.data for node in content[-1].childNodes]
    includes.insert(0,"generic")
    families ={}
    #Handle each family to be included
    for include in includes :
        #parse family .qin file
        relPath = os.path.join("sheets","evaluations",
                             properties["qsosappname"],properties["release"],
                             include + ".qscore")
        absPath = os.path.join(repositoryroot, relPath)
        sheet = "".join(line.strip() for line in file(absPath).readlines())
        xml = minidom.parseString(sheet).firstChild
        
        
        author = getAuthor(repositoryroot, relPath)
        
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
        families[include]=family.family(author, scores, comments)
        
    #Create and return the expected documents
    return document.Document(properties,families)
    
##
#    @ingroup builder
#
def assembleSheet(document, repositoryroot):
    """
    Generate the qsos XML file corresponding to givent document object
    
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
    
    auths = {}
    #Add blank qsos evaluation of families
    for item in document["families"] :
        (name, mail) = document[item]['author']
        auths[name] = mail
        include = os.path.join(repositoryroot,"sheets","families",item + ".qin")
        include = "".join(line.strip() for line in file(include).readlines())
        include = minidom.parseString(include).firstChild
        for section in include.childNodes[1:] : root.appendChild(section)
    
    #Fill-in authors tag with data extracted 
    for k, v in auths.iteritems() :
        tag = sheet.createElement('author')
        leaf = sheet.createElement("name")
        leaf.appendChild(sheet.createTextNode(k))
        tag.appendChild(leaf)
        leaf = sheet.createElement("email")
        leaf.appendChild(sheet.createTextNode(v))
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
    for item in document["families"] :
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
    