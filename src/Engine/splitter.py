"""
QSOS-Engine's splitter module.

This module provides all required tools for splitting a raw QSOS-Evaluation into
new qscores files, stored into the convenient folder of local repository copy.
splitter
"""

##
#    @defgroup splitter Splitter 
#    @ingroup Engine
#    @author Hery Randriamanamihaga




from Engine import document
from Engine import family
from xml.dom import minidom

import os


##
#    @ingroup splitter
#        
def parse(document,repositoryroot):
    """
    Parse a qsos evaluation and build dictionnary containing qscore evaluations.
    Dictionnary keys are path to qscore file and values are qscore contents which
    are element tag's score and contents for each family declared in qsosappfamily
    evaluation's tag 
    
    
    
    @param document
            document object of evaluation to be parsed
    
    @param repositoryroot
            path to root of local copy of repository.
            Default value is ..
            
    @return dictionnary of parsed document
    """
     
    base = os.path.join("sheets", "evaluations",
                        document["properties"]["qsosappname"],
                        document["properties"]["release"]
                        )
    tree = dict((base+"/"+f+".qscore", createScore(document[f])) for f in document.families)
    tree[base+"/"+"header.qscore"] = createHeader(document)
    
    return tree
        
    
##
#    @ingroup splitter
#
def createDocument(evaluation,familypath="../sheets/families"):
    """
    Creates a document object  from qsos raw evaluation. Relevant elements are
    extracted from families modelsheets (elements with sub-elements are skipped
    as they do not have any score nor comment tag) and their evaluations are
    extracted from the qsos evaluation. The authors and dates are the same
    (evaluation's in fact) for each family. Generic section of qsos evaluation
    is also added to families component of a document. 
    
    @param evaluation
            Qsos evaluation's string flow
    
    @param familypath
            Path to families model sheets.
            Default value is ../sheets/families
        
    @return
        Evaluation's representation's document object
    """
    rawDocument = minidom.parseString(evaluation)
    #Define the ID attribute of each element tag of the raw document
    for element in rawDocument.getElementsByTagName("element"):
        element.setIdAttribute("name")
        
    #Create the property list from the content of header.
    #the first, second and last tag of header are ignored
    #as they are not document properties but part of families contents
    header = rawDocument.firstChild.firstChild.childNodes
    properties = {}
    for n in header[2:-1]:
        try :
            properties[n.tagName] = n.firstChild.data
        except AttributeError :
            properties[n.tagName] = "N/A"
                            
    
    #Instantiate a QSOS-Document object initiated with the properties extracted
    #from XML evaluation and empty family dictionnary
    qsos = document.Document(properties,{})
    
    #Extract  relevant information from the raw evaluation:
    #    - authors
    #    - dates
    #    - families (generic section is manually as it appears in each evaluation
    
    # TODO : Each information should be probed in case no values are
    #         provided in the XML document
    
    authors = [(item.firstChild.firstChild.data, item.lastChild.firstChild.data)
               for item in header[0].childNodes]
#    dates = (header[1].firstChild.firstChild.data, header[1].lastChild.firstChild.data)
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
        f = family.family(authors, {}, {})
        for element in template.getElementsByTagName("desc0"):
            name = element.parentNode.getAttribute("name")
            
            # TODO : use a logger for AttributeError exception^^
        
            try :
                f.scores[name] = rawDocument.                                   \
                                    getElementById(name).                       \
                                    getElementsByTagName("score").              \
                                    item(0).firstChild.data
            except AttributeError :
#                print "No score found for element %s" % (name,)
                pass    
            try :
                f.comments[name] = rawDocument.                                 \
                                    getElementById(name).                       \
                                    getElementsByTagName("comment")             \
                                    .item(0).firstChild.data
            except AttributeError :
#                print "No comment found for element %s" % (name,)
                pass
        #End of iteration, just add the family in document object
        qsos.families[include] = f
    return qsos

def createHeader(document):
    document = minidom.Document()
    root = document.createElement("qsosheader")
    document.appendChild(root)
    for element in document["properties"]:
        tag = document.createElement(element)
        tag.appendChild(document.createElement(document["properties"][element]))
    return document.toprettyxml("\t", "\n", "utf-8")

##
#    @ingroup splitter
#
def createScore(family):
    """
    Creates the qscore XML document of family object evaluation content to be
    stored on the local copy of repository.
    
    @param family 
            The family object to be transformed
    
    @return
        XML formatted family's qscore sheet
    """
    
    #Create the return DOM and root element <qsosscore>
    document = minidom.Document()
    root = document.createElement("qsosscore")
    
    #Build score section
    #Local copies of family's attribute are made as destructive
    #iterator are used.
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
        root.appendChild(tag)
    
    #Remaining items of comments dictionnary are added to output XML
    #No score tags for these elements as there must be no item left in scores 
    while comments :
        (name, value) = comments.popitem()
        tag = document.createElement("element")
        tag.setAttribute("name", name)
        leaf = document.createElement("comment")
        leaf.appendChild(document.createTextNode(value))
        tag.appendChild(leaf)
        root.appendChild(tag)
    
    #Build the final document
    document.appendChild(root)    
    
    #Pretty format ant return the result qsos score sheet
    return document.toprettyxml("\t", "\n", "utf-8")
