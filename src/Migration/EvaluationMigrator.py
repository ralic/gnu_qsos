"""
QSOS-Engine's evaluation migration tool.

This module provides migrates an old qsos evaluation to new evaluation format
"""

from xml.dom import minidom
from xml.dom import Node

from Engine import core 

import os

def UpgradeEvaluationSheet(sheet, outDir, Repo):
    #Read evaluation
    
    evaluation = minidom.parse(sheet)
    
    #Create output document
    output = minidom.Document()
    root = output.createElement("document")
    header = output.createElement("header")
    
    #Initialize empty header and copy sections
    root.appendChild(header)
    for section in evaluation.firstChild.childNodes[3:] : root.appendChild(section)
    
    #Fill header with authors and dates
    header.appendChild(evaluation.firstChild.getElementsByTagName("authors")[0])
    header.appendChild(evaluation.firstChild.getElementsByTagName("dates")[0])


    #Fill header properties from template
    appname = evaluation.firstChild.getElementsByTagName("qsosappname")[0].firstChild.data
    template = minidom.parse(os.path.join(Repo,"sheets","templates",appname + ".qtpl"))
    properties = ["language", "appname", "licenseid", "licensedesc", "url", "desc", "demourl", "qsosappname", "qsosspecificformat"]
    for node in properties :
        element = template.firstChild.getElementsByTagName(node)
        try : header.appendChild(template.firstChild.getElementsByTagName(node)[0])
        except IndexError : pass
    
    #Set qsosformat to 2.0
    format = output.createElement("qsosformat")
    format.appendChild(output.createTextNode("2.0"))
    header.appendChild(format)
        
    #Set release
    release = evaluation.firstChild.getElementsByTagName("release")[0].firstChild.data
    version = output.createElement("release")
    version.appendChild(output.createTextNode(release))
    header.appendChild(version)
    
    #Add appfamilies
    header.appendChild(template.firstChild.getElementsByTagName("template")[0].getElementsByTagName("qsosappfamilies")[0])
    
    #Write-out new evaluation sheet
    output.appendChild(root)
    file = open(outDir + "/" + appname + "-" + release + ".qsos",'w')
    file.write(output.toxml("utf-8"))
    file.close()

def UpgradeRepository(inDir, outDir, Repo):
    #Statistics vars
    Errors = []
    Upgraded = []
    Processed = 0
    Ignored = 0
    for filename in os.listdir(inDir):
        Processed = Processed + 1
        #Ignore not qsos files
        if not filename.split(".")[-1] == "qsos" :
            Ignored = Ignored +1
            pass
        #Upgrade qsos files
        else :
            try: 
                UpgradeEvaluationSheet(inDir + "/" + filename, outDir, Repo)
                Upgraded.append(filename)
            except Exception, inst :
                Errors.append("Error occured when upgrading " + filename + ":" + str(inst))
                
    #Print output
    for e in Errors : print e
    print "Failed %s files" % (len(Errors),)
    print "Upgraded %s files" % (len(Upgraded),)
    print "Processed %s files" % (Processed,)
    print "Ignored %s files" % (Ignored,)
    
def CommitRepository(inDir, Repo):
    Errors = []
    Submitted = []
    Processed = 0
    Ignored = 0
    core.setup(Repo)
    for filename in os.listdir(inDir) :
        Processed = Processed + 1
        if not filename.split(".")[-1] == "qsos" :
            Ignored = Ignored +1
            pass
        else :
            try: 
                #Extract information from qsos file
                CleanWhiteSpaces(inDir + "/" + filename)
                evaluation = minidom.parse(inDir + "/" + filename)
                email = evaluation.firstChild.firstChild.firstChild.firstChild.getElementsByTagName("email")[0].firstChild.data
                name = evaluation.firstChild.firstChild.firstChild.firstChild.getElementsByTagName("name")[0].firstChild.data
                message = "Auto-uplodaded file : " + filename
                eval = FileObject(file(inDir + "/" + filename), filename)
            except Exception, inst :
                Errors.append("Error occured when processing " + filename + ":" + str(inst))
                
  
            try:
                #Invoke core submit method
                core.submit({"Author":name, "E-mail":email,  "Description":message, "Type":"Evaluation", "File":eval})
                Submitted.append(filename)
            except Exception, inst :
                Errors.append("Error occured when submitting " + filename + ":" + str(inst))
                
    for e in Errors : print e
    print "Failed %s files" % (len(Errors),)
    print "Submitted %s files" % (len(Submitted),)
    print "Processed %s files" % (Processed,)
    print "Ignored %s files" % (Ignored,)

def CleanWhiteSpaces(xml):
    parent = minidom.parse(xml).firstChild
    removeWhitespaceNodes(parent)
    clean = minidom.Document()
    clean.appendChild(parent)
    file = open(xml,'w')
    file.write(clean.toxml("utf-8"))
    file.close()
    
def removeWhitespaceNodes(parent):
     for child in list(parent.childNodes):
       if child.nodeType==child.TEXT_NODE and child.data.strip()=='':
         parent.removeChild(child)
       else:
         removeWhitespaceNodes(child)

class FileObject ():
    def __init__(self,content,name):
        self.file = content
        self.filename = name 
