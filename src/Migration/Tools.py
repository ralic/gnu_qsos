   
from xml.dom import minidom
from xml.dom import Node

def removeWhitespaceNodes(parent):
    """
    Remove whitespaces children nodes of an XML tree
    
    Side effect :
            Whitespaces children (TextNodes which are combination of \n, \t or space) are
            removed from subtree of the parent node
            
    @param parent
            Parent node of subtree to clean
    """
    #Recursive function
    for child in list(parent.childNodes):
        #Terminal case : current child is a text node and it needs cleaning
        if child.nodeType == child.TEXT_NODE and child.data.strip() == '' :
            parent.removeChild(child)
        #Non terminal case : proceed on subtree
        else: removeWhitespaceNodes(child)

def readAndCleanXML(xml):
    """
    Read and clean XML file from whitespaces
    
    @param xml
            Path to XML file
    @return
            Minidom parsed xml with no whitespaces
    """
    clean = minidom.parse(xml)
    removeWhitespaceNodes(clean.firstChild)
    return clean

def report(errors, success):
    """
    Write a report on standard output 
    
    @param errors
            List of encountered errors
    @param success
            Number of successful operations
    """
    for e in errors : print e
    print """
    Failed %s files
    Upgraded %s files
    """ % (len(errors), success)
    
class FileObject ():
    """
    File wrapper for auto-commit data
    
    Provide a structure simulating formless's FileUpload object (with file and filename fields)
    """
    def __init__(self, content, name):
        self.file = content
        self.filename = name 
