from Engine import document
from xml.dom import minidom

def parse(evaluation):
    """Parses an evaluation
    
    Parameter : 
        - evaluation    -   string of evaluation XML flow
        
    Returns document"""
    pass

def createDocument(evaluation,familypath="../../sheets/families"):
    rawDocument = minidom.parseString(evaluation)
    #Define the ID attribute of each element tag of the raw document
    for element in rawDocument.getElementsByTagName("element"):
        element.setIdAttribute("name")
        
    section = rawDocument.firstChild.firstChild.childNodes
    #Create the property list from the content of header.
    #the first, second and last tag of header are ignored
    #as they are not document properties but part of families contents
    properties = [str(node.firstChild.data) for node in section[2:-2]]
    
    #The same header applies for each family evaluation
#    header = minidom.Element("header")
#    for item in section[0:2] :
#        header.appendChild(item)
#    sections = [item.firstChild.data for item in section[-1].childNodes]

    #Instantiate a QSOS-Document object initiated with the properties extracted
    #from XML evaluation and empty family dictionnary
    #At least, one author must be identified for the document
    qsos = document.Document(properties,{})
    authors = dict([(item.firstChild.firstChild.data, item.lastChild.firstChild.data) for item in section[0].childNodes])
    
    #dates must be tested
    dates = (section[1].firstChild.firstChild.data, section[1].lastChild.firstChild.data)
    
    
    families = [node.firstChild.data for node in section[-1].childNodes]
    for include in families :
        template = minidom.parse("/".join([familypath,".".join([families[0],"qin"])]))
        for element in template.getElementsByTagName("desc0"):
            name = element.parentNode.getAttribute("name")
            print rawDocument.getElementById(name).getElementsByTagName("score").item(0).firstChild
            
    #Each family is assumed to bo a section xml fragment
    for section in rawDocument.getElementsByTagName("section") :
        f = document.family(authors, dates)
        scores = {}
        for score in section.getElementsByTagName("score") :
            try :
                value = score.firstChild.data
            except AttributeError :
                value = ""
            scores[score.parentNode.getAttribute("name")]=value
        f.scores = scores
        qsos.families[section.getAttribute("name")]=f
    return qsos

def toXML(document):
    for f in document.families :
        evaluation = minidom.Document()
        root = evaluation.createElement("qsosscore")
        evaluation.appendChild(root)
        print document[f]["authors"]
    pass

def createScore(familyEvaluation):
    """Creates the XML document to be stored on the 
    filesystem of the evaluation of a given family"""
    
    #Parse first the xmlflow to a DOM
    evaluation = minidom.parseString(familyEvaluation)
    nodes = evaluation.firstChild.childNodes
    
    #Create the return DOM with root element <qsosscore>
    document = minidom.Document()
    root = document.createElement("qsosscore")
    document.appendChild(root)
    
    #Create and fill the header to the return DOM
    header = document.createElement("header")
    header.appendChild(nodes[0].getElementsByTagName("authors")[0])
    header.appendChild(nodes[0].getElementsByTagName("dates")[0])
    
    #Append the header to the DOM
    root.appendChild(header)
    
    
    
    #Pretty format ant return the result qsos score sheet
    return document.toprettyxml("\t", "\n", "utf-8")

def createSection(name):
    """Creates an empty section with its name attribute
    
    Parameters :
        - name    -    the value of name attribute of the section
        
    Returns string."""

    section = minidom.Element("section")
    section.setAttribute("name", name)
    
    return section    

def createElement(name, score="", comment=""):
    """Creates an element for qsos-score sheet
    
    Parameters :
        - name     -    the value of name attribute of the element
        - score    -    the score of the item (optional)
        - comment  -    a comment about the evalutation of the item (optional)
        
    Returns string."""
    
    #Create the element and initialize its name attribute
    element = minidom.Element("element")
    element.setAttribute("name", name)
    
    #Create and append the score tag if a score is provided
    if score != "" :
        value = minidom.Element("score")
        value.appendChild(minidom.Document().createTextNode(score))
        element.appendChild(value)
    
    #Create and append the comment tag if any comment is provided
    if comment != "" :
        desc = minidom.Element("desc")
        desc.appendChild(minidom.Document().createTextNode(comment))
        element.appendChild(desc)
    
    return element

def split(document):
    """Splits an evaluation
    
    Parameter :
        - document    -    the document representation of to be splitted evaluation
        
    Returns  {{(string,string}}"""
    pass