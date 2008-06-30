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
        sheet = os.path.join(repositoryroot,"sheets","evaluations",
                             properties["qsosappname"],properties["release"],
                             include + ".qscore")
        sheet = "".join(line.strip() for line in file(sheet).readlines())
        xml = minidom.parseString(sheet).firstChild
        
        #extract directly interesting information :
        #    - authors
        #    - dates
        [h, elt] = xml.childNodes
        [a,d]=h.childNodes
        
        authors =[(n.firstChild.data,e.firstChild.data) 
                  for [n,e] in [i.childNodes for i in a.childNodes]]
        #Dates can be not provided on .qin files, in which case, empty string
        #is assumed to be date value
        value = lambda x : (x and [x.data] or [""])[0]
        dates = (value(d.firstChild.firstChild),value(d.lastChild.firstChild))
        
        #Scores and comments extraction loop
        scores = {}
        comments = {}
        for node in elt.childNodes :
            n = node.getAttribute("name")
            v = node.getElementsByTagName("score")
            if v : scores[n] = v[0].firstChild.data 
            v = node.getElementsByTagName("comment")
            if v : comments[n] = v[0].firstChild.data
            
        #Add the family into family list for the document 
        families[include]=family.family(authors, dates, scores, comments)
        
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
    dates = sheet.createElement("dates")
    header.appendChild(authors)
    header.appendChild(dates)
    root.appendChild(header)
    
    #Fill in header with properties
    for item in document["properties"] :
        tag = sheet.createElement(item)
        tag.appendChild(sheet.createTextNode(document["properties"][item]))
        header.appendChild(tag)

    #Add blank qsos evaluation of families
    families = document["families"].keys()
    for item in families :
        include = os.path.join(repositoryroot,"sheets","families",item + ".qin")
        include = "".join(line.strip() for line in file(include).readlines())
        include = minidom.parseString(include).firstChild
        for section in include.childNodes[1:] : root.appendChild(section)
            
    
    #Finalize the blank  document
    sheet.appendChild(root)
    print document["families"]
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