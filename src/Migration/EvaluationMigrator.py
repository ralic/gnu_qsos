"""
QSOS-Engine's evaluation migration tool.

This module provides migrates an old qsos evaluation to new evaluation format
"""

from xml.dom import minidom
from xml.dom import Node

from Engine import core 

import os

def UpgradeEvaluationSheet(sheet, outDir, Repo):
    """
    Upgrades a QSOS evaluation sheet from old format to 2.0 format
    
    Side effect :
            The upgraded evaluation sheet is created into the outDir folder
            
    @param sheet
            The sheet to be upgraded
    @param outDir
            Path to output directory
    @param Repo
            Path to QSOS repository
    """
    
    #Read and clean evaluation
    evaluation = readAndCleanXML(sheet)
    
    #Create structure of output document
    output = minidom.Document()
    root = output.createElement("document")
    output.appendChild(root)
    
    #Create header
    header = output.createElement("header") 
    root.appendChild(header)
    
    #Copy sections 
    for section in evaluation.firstChild.childNodes[1:] : root.appendChild(section)
    
    #Import authors and dates
    header.appendChild(evaluation.firstChild.getElementsByTagName("authors")[0])
    header.appendChild(evaluation.firstChild.getElementsByTagName("dates")[0])


    #Re-build header properties from repository's template
    appname = evaluation.firstChild.getElementsByTagName("qsosappname")[0].firstChild.data
    template = readAndCleanXML(os.path.join(Repo, "sheets", "templates", appname + ".qtpl"))
    properties = ["language", "appname", "licenseid", "licensedesc","url",
                  "desc", "demourl", "qsosappname", "qsosspecificformat"]
    for node in properties :
        try :
            header.appendChild(template.firstChild.getElementsByTagName(node)[0])
        except IndexError :
            pass
    
    #Set qsosformat to 2.0
    format = output.createElement("qsosformat")
    format.appendChild(output.createTextNode("2.0"))
    header.appendChild(format)
        
    #Set correct release from evaluation sheet
    release = evaluation.firstChild.getElementsByTagName("release")[0].firstChild.data
    version = output.createElement("release")
    version.appendChild(output.createTextNode(release))
    header.appendChild(version)
    
    #Add appfamilies
    header.appendChild(template.firstChild.getElementsByTagName("template")[0].getElementsByTagName("qsosappfamilies")[0])
    
    #Write-out new evaluation sheet
    file = open(outDir + "/" + appname + "-" + release + ".qsos", 'w')
    file.write(output.toxml("utf-8"))
    file.close()

def UpgradeRepository(inDir, outDir, Repo):
    """
    Upgrades a collection of QSOS evaluation sheets from old format to 2.0 format
    
    Side effect :
            The upgraded evaluation sheets are created into the outDir folder
            
    @param inDir
            Path to directory of evaluations to be upgraded
    @param outDir
            Path to output directory
    @param Repo
            Path to QSOS repository
    """
    #Statistics vars
    Errors = []
    Upgraded = 0
    Processed = 0
    Ignored = 0
    
    #Main loop : skip not qsos file and upgrade qsos files
    for filename in os.listdir(inDir):
        if not filename.split(".")[ - 1] == "qsos" : Ignored = Ignored + 1
        #Upgrade qsos files
        else :
            try: 
                UpgradeEvaluationSheet(inDir + "/" + filename, outDir, Repo)
                Upgraded += 1
            except Exception, inst :
                Errors.append("Error occured when upgrading " + filename + ":" + str(inst))
        Processed += 1

    #Write a report on output
    report(Errors, Upgraded, Processed, Ignored)
    
def CommitRepository(inDir, Repo):
    """
    Auto-commit a collection of upgraded QSOS evaluation sheets
    
    Side effect :
            Auto-commit on git repository
            
    @param inDir
            Path to directory of evaluations to be auto-commited
    @param Repo
            Path to QSOS repository
    """
    #Statistics vars    
    Errors = []
    Submitted = 0
    Processed = 0
    Ignored = 0
    
    #Setup git repository
    core.setup(Repo)
    
    #Main loop : prepare data for auto-commit and proceed
    for filename in os.listdir(inDir) :
        if not filename.split(".")[ - 1] == "qsos" : Ignored += 1
        else :
            #Extract information from qsos file
            try: 
                evaluation = readAndCleanXML(inDir + "/" + filename)
                email = evaluation.firstChild.firstChild.firstChild.firstChild.getElementsByTagName("email")[0].firstChild.data
                name = evaluation.firstChild.firstChild.firstChild.firstChild.getElementsByTagName("name")[0].firstChild.data
                message = "Auto-uplodaded file : " + filename
                eval = FileObject(file(inDir + "/" + filename), filename)
            except Exception, inst :
                Errors.append("Error occured when processing " + filename + ":" + str(inst))
            
            #Invoke core submit method
            try:
                core.submit({"Author":name, "E-mail":email, "Description":message, "Type":"Evaluation", "File":eval})
                Submitted += 1
            except Exception, inst :
                Errors.append("Error occured when submitting " + filename + ":" + str(inst))
        Processed = Processed + 1
    
    #Write a report on output
    report(Errors, Submitted, Processed, Ignored)


    
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

def report(errors, success, processed, ignored):
    """
    Write a report on standard output 
    
    @param errors
            List of encountered errors
    @param success
            Number of successful operations
    @param processed
            Total number of processed items
    @param ignored
            Number of ignored items
    """
    for e in errors : print e
    print """
    Failed %s files
    Upgraded %s files
    Processed %s files
    Ignored %s files
    """ % (len(errors), success, processed, ignored)
    
class FileObject ():
    """
    File wrapper for auto-commit data
    
    Provide a structure simulating formless's FileUpload object (with file and filename fields)
    """
    def __init__(self, content, name):
        self.file = content
        self.filename = name 
