from Engine import document
from Engine import family
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
        
    #Create the property list from the content of header.
    #the first, second and last tag of header are ignored
    #as they are not document properties but part of families contents
    header = rawDocument.firstChild.firstChild.childNodes
    properties = [str(node.firstChild.data) for node in header[2:-2]]
    
    #Instantiate a QSOS-Document object initiated with the properties extracted
    #from XML evaluation and empty family dictionnary
    qsos = document.Document(properties,{})
    
    #Extract  relevant information from the raw evaluation:
    #    - authors
    #    - dates
    #    - families (generic section is manually as it appears in each evaluation
    
    # TODO : Each information should be probed in case no values are
    #         provided in the XML document
    
    authors = [(item.firstChild.firstChild.data, item.lastChild.firstChild.data) for item in header[0].childNodes]
    dates = (header[1].firstChild.firstChild.data, header[1].lastChild.firstChild.data)
    families = [node.firstChild.data for node in header[-1].childNodes]
    families.insert(0,"generic")

    #Build the Family object for each family component of the evaluation :
    #    - Extract from repository the family sheet (.qin files)
    #    - Read the scores and comments from evaluation
    #    - Update entry in family object
    for include in families :
        template = minidom.parse("/".join([familypath,".".join([families[0],"qin"])]))
        #Initiate the family object : 
        #    -same authors and dates for all families of the same evaluation
        #    -empty score and comments dictionnary
        f = family.family(authors, dates)
        for element in template.getElementsByTagName("desc0"):
            name = element.parentNode.getAttribute("name")
            
            # TODO : use a logger for AttributeError exception^^
        
            try :
                f.scores[name] = rawDocument.getElementById(name).getElementsByTagName("score").item(0).firstChild.data
            except AttributeError :
                print "No score found for element %s" % (name,)
            try :
                f.comments[name] = rawDocument.getElementById(name).getElementsByTagName("comment").item(0).firstChild.data
            except AttributeError :
                print "No comment found for element %s" % (name,)
        #End of iteration, just add the family in document object
        qsos.families[include] = f
        print createScore(qsos.families["generic"])
    return qsos

def toXML(document):
    for f in document.families :
        evaluation = minidom.Document()
        root = evaluation.createElement("qsosscore")
        evaluation.appendChild(root)
        print document[f]["authors"]
    pass

def createScore(family):
    """Creates the XML document to be stored on the 
    filesystem of the evaluation from a family object
    
    Returns : string    String flow of the family object"""
    
    #Create the return DOM and root element <qsosscore>
    document = minidom.Document()
    root = document.createElement("qsosscore")
    
    #Build header which contains only author and dates
    header = document.createElement("header")
    for author in family["authors"] :
        tag = document.createElement("author")
        leaf = document.createElement("name")
        leaf.appendChild(document.createTextNode(author[0]))
        tag.appendChild(leaf)
        leaf = document.createElement("email")
        leaf.appendChild(document.createTextNode(author[1]))
        tag.appendChild(leaf)                           
        header.appendChild(tag)
    
    tag = document.createElement("dates")
    leaf = document.createElement("creation")
    leaf.appendChild(document.createTextNode(family["date.creation"]))
    tag.appendChild(leaf)
    leaf = document.createElement("validation")
    leaf.appendChild(document.createTextNode(family["date.validation"]))
    tag.appendChild(leaf)
    header.appendChild(tag)
    
    #Build score section
    section = document.createElement("scores")

    
    #Build the final document
    root.appendChild(header)
    root.appendChild(section)
    document.appendChild(root)    
    
    #Pretty format ant return the result qsos score sheet
    return document.toprettyxml("\t", "\n", "utf-8")

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