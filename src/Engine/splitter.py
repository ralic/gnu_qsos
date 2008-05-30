from Engine import document
from Engine import family
from xml.dom import minidom
import os

def parse(evaluation,repositoryroot="../.."):
    """Parses a qsos evaluation and creates/overwrite qscore files containing
    elements' scores and comments for each family declared in qsosappfamily
    evaluation's tag.
    
    The tree structure under sheet directory may also be modified as qscore files
    are created into appname/version directory. (appname and version are also
    extracted from evaluation's header)
    
    Parameters : 
        - evaluation     -   string of evaluation XML flow
        - repositoryroot -   path to root of local copy of repository
            default value of repositoryroot is ../..
    """
    #Transform XML flow into document object
    document = createDocument(evaluation)
    
    #Create tree folder in filesystem
    #makedirs fails with OSError 17 whenever the directory to make
    #already exists. This specific error is excepted 
    path = os.path.join(repositoryroot,"sheets","evaluations",document["properties"][-1],document["properties"][2])
    try :
        os.makedirs(path)
    except OSError, error :
        if error[0] != 17 : raise OSError, error
    
    #As the folder is created, can be added into.
    for f in document.families :
        file = open(os.path.join(path,".".join([f,"qscore"])),'w')
        file.write(createScore(document[f]))
        file.close()

def createDocument(evaluation,familypath="../../sheets/families"):
    """Creates a document object  from qsos raw evaluation
    Relevant elements are extracted from families modelsheets (elements
    with sub-elements are skipped as they do not have any score nor 
    comment tag) and their evaluations are extracted from the qsos
    evaluation. The authors and dates are the same (evaluation's in fact)
    for each family. Generic section of qsos evaluation is also added to
    families component of a document. 
    
    
    Parameters :
        - evaluation    -    string flow of qsos evaluation
        - familypath    -    path to directory of families modelsheet
            default value is ../../sheets/families
        
    Returns
        document        -    document object of representation of evaluation
        """
    rawDocument = minidom.parseString(evaluation)
    #Define the ID attribute of each element tag of the raw document
    for element in rawDocument.getElementsByTagName("element"):
        element.setIdAttribute("name")
        
    #Create the property list from the content of header.
    #the first, second and last tag of header are ignored
    #as they are not document properties but part of families contents
    header = rawDocument.firstChild.firstChild.childNodes
    properties = [node.firstChild.data for node in header[2:-2]]
    
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
        template = minidom.parse("/".join([familypath,".".join([include,"qin"])]))
        #Initiate the family object : 
        #    -same authors and dates for all families of the same evaluation
        #    -empty score and comments dictionnary
        f = family.family(authors, dates, {}, {})
        for element in template.getElementsByTagName("desc0"):
            name = element.parentNode.getAttribute("name")
            
            # TODO : use a logger for AttributeError exception^^
        
            try :
                f.scores[name] = rawDocument.getElementById(name).getElementsByTagName("score").item(0).firstChild.data
            except AttributeError :
                print "No score found for element %s" % (name,)
                pass    
            try :
                f.comments[name] = rawDocument.getElementById(name).getElementsByTagName("comment").item(0).firstChild.data
            except AttributeError :
                pass
                print "No comment found for element %s" % (name,)
        #End of iteration, just add the family in document object
        qsos.families[include] = f
    return qsos


def createScore(family):
    """Creates the qscore XML document of family object evaluation
    content to be stored on the local copy of repository
    
    Parameter :
        family    -    the family object to be transformed
    
    Returns
        string    -    XML formatted family's qscore sheet"""
    
    #Create the return DOM and root element <qsosscore>
    document = minidom.Document()
    root = document.createElement("qsosscore")
    
    #Build header which contains only author and dates
    header = document.createElement("header")
    toplevel = document.createElement("authors")
    for author in family["authors"] :
        tag = document.createElement("author")
        leaf = document.createElement("name")
        leaf.appendChild(document.createTextNode(author[0]))
        tag.appendChild(leaf)
        leaf = document.createElement("email")
        leaf.appendChild(document.createTextNode(author[1]))
        tag.appendChild(leaf)
        toplevel.appendChild(tag)
    header.appendChild(toplevel)
    tag = document.createElement("dates")
    leaf = document.createElement("creation")
    leaf.appendChild(document.createTextNode(family["date.creation"]))
    tag.appendChild(leaf)
    leaf = document.createElement("validation")
    leaf.appendChild(document.createTextNode(family["date.validation"]))
    tag.appendChild(leaf)
    header.appendChild(tag)
    
    #Build score section
    #Local copies of family's attribute are made as destructive
    #iterator are used.
    section = document.createElement("scores")
    scores = family.scores.copy()
    comments = family.comments.copy()
    
    #Elements with score tags are generated first. Corresponding
    #comments are also added to elements tag (and removed from
    #comments dictionnary)
    while scores :
        (name,value) = scores.popitem()
        tag = document.createElement("element")
        tag.setAttribute("name", name)
        leaf = document.createElement("score")
        leaf.appendChild(document.createTextNode(value))
        tag.appendChild(leaf)
        if name in comments :
            leaf = document.createElement("comment")
            leaf.appendChild(document.createTextNode(comments.pop(name)))
            tag.appendChild(leaf)
        section.appendChild(tag)
    
    #Remaining items of comments dictionnary are added to output XML
    #No score tags for these elements as there must be no item left in scores 
    while comments :
        (name, value) = comments.popitem()
        tag = document.createElement("element")
        tag.setAttribute("name", name)
        leaf = document.createElement("comment")
        leaf.appendChild(document.createTextNode(value))
        tag.appendChild(leaf)
        section.appendChild(tag)
    
    #Build the final document
    root.appendChild(header)
    root.appendChild(section)
    document.appendChild(root)    
    
    #Pretty format ant return the result qsos score sheet
    return document.toprettyxml("\t", "\n", "utf-8")