from Engine import document
from xml.dom import minidom

def parse(evaluation):
    """Parses an evaluation
    
    Parameter : 
        - evaluation    -   string of evaluation XML flow
        
    Returns document"""
    pass

def createScore(familyEvaluation):
    """Creates the XML document to be stored on the 
    filesystem of the evaluation of a given family"""
    
    #Parse first the xmlflow to a DOM
    evaluation = minidom.parseString(familyEvaluation)
    
    #Create the return DOM with root element <qsosscore>
    document = minidom.Document()
    root = document.createElement("qsosscore")
    document.appendChild(root)
    
    #Create and append the header to the return DOM
    header = createEmptyHeader()
    root.appendChild(header)
    
    #Pretty format ant return the result qsos score sheet
    return document.toprettyxml("\t", "\n", "utf-8")


def createEmptyHeader():
    """Create an empty header fragment for a QSOS-* flow
    
    Returns string."""
    #Create header element
    header = minidom.Element("header")
    
    #Create and append authors tag to header
    authors = minidom.Element("authors")
#    authors.appendChild(createAuthor(["author name","em@il"]))
    header.appendChild(authors) 
    
    #Create and append blank dates structure to header
    dates = minidom.Element("dates")
    dates.appendChild(minidom.Element("creation"))
    dates.appendChild(minidom.Element("validation"))
    header.appendChild(dates)
    
    return header

def createAuthor(authorInfo):
    """Create an author fragment for a QSOS-* XML flow
    
    Parameter :
        - authorInfo    -    String list with [name,email]
        
    Returns string."""
    
    #Toplevel tag
    author = minidom.Element("author")
    
    #Name tag
    name = minidom.Element("name")
    name.appendChild(minidom.Document().createTextNode(authorInfo[0]))
    author.appendChild(name)
    
    #email tag
    email = minidom.Element("email")
    email.appendChild(minidom.Document().createTextNode(authorInfo[1]))
    author.appendChild(email)
    
    return author

def createSection(name):
    """Creates an empty section with its name attribute
    
    Parameters :
        - name    -    the value of name attribute of the section
        
    Returns string."""

    section = minidom.Element("section")
    section.setAttribute("name", name)
    
    return section    

def createElement(name,score="",comment=""):
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