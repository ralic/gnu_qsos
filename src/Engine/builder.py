from Engine import document
from Engine import family
from xml.dom import minidom
import os

def build(name, version, repositoryroot="../.."):
    #Read the template of requested evaluation from repository
    template = "-".join([name,version])
    template = ".".join([template,"qtpl"])
    template = os.path.join(repositoryroot,"sheets","templates",template)
    template = file(template).read()
    template = "".join([line.strip() for line in (template.splitlines())])
    
    #Extract template contents
    content = minidom.parseString(template).firstChild.lastChild.childNodes
    
    #Extract properties from template contents
    properties = dict([(node.tagName,node.firstChild.data) for node in content[0:-2]])
    
    #Build families object according to families declared from template
    #generic *family* must also be added to families
    includes = [node.firstChild.data for node in content[-1].childNodes]
    includes.insert(0,"generic")
    families ={}
    #Handle each family to be included
    for include in includes :
        #parse family .qin file
        familySheet = ".".join([include,"qscore"])
        familySheet = os.path.join(repositoryroot,"sheets","evaluations",name,version,familySheet)
        familySheet = file(familySheet).read()
        familySheet = "".join([line.strip() for line in familySheet.splitlines()])
        xml = minidom.parseString(familySheet).firstChild
        
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
    
    
def assembleSheet(document, repositoryroot="../.."):
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
    families = document["families"].keys()
    
    #Add blank qsos evaluation of families
    for item in families :
        include = ".".join([item, "qin"])
        include = os.path.join(repositoryroot,"sheets","families",include)
        include = file(include).read()
        include = "".join([line.strip() for line in include.splitlines()])
        include = minidom.parseString(include).firstChild
        for section in include.childNodes[1:]:
            root.appendChild(section)
    
    #Finalize the blank  document
    sheet.appendChild(root)
    
            
    sheet = minidom.parseString(sheet.toxml())
    #Define ID tag for the document
    for elements in sheet.getElementsByTagName("element") :
        elements.setIdAttribute("name")
    #Fill-in evaluation with families data
    for item in document["families"] :
        scores = document[item].scores.copy()
        comments = document[item].comments.copy()
        #Iterate first from elements with scores
        while scores :
            (element,score) = scores.popitem()
            e =  sheet.getElementById(element)
            if not e.getElementsByTagName("score") :
                e.appendChild(sheet.createElement("score"))
            e.getElementsByTagName("score").item(0).appendChild(sheet.createTextNode(score))
            if element in comments :
                if not e.getElementsByTagName("comment"):
                    e.appendChild(sheet.createElement("comment"))
                e.getElementsByTagName("comment").item(0).appendChild(sheet.createTextNode(comments.pop(element)))
        while comments :
            (element, comment) = comments.popitem()
            e =  sheet.getElementById(element)
            if not e.getElementsByTagName("comment"):
                e.appendChild(sheet.createElement("comment"))
            e.getElementsByTagName("comment").item(0).appendChild(sheet.createTextNode(comment))
    return sheet.toprettyxml("\t", "\n", "utf-8")

def toqsos(sheet, document):
    pass