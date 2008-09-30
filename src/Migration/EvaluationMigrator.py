"""
QSOS-Engine's evaluation migration tool.

This module provides migrates an old qsos evaluation to new evaluation format
"""

from xml.dom import minidom
from xml.dom import Node

from Engine import core 

from Tools import FileObject
from Tools import report
from Tools import readAndCleanXML

import os

def UpgradeEvaluationSheet(sheet, family, tmpDir):
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
    try :
        appname = evaluation.firstChild.getElementsByTagName("qsosappname")[0].firstChild.data
    except Exception :
        appname = evaluation.firstChild.getElementsByTagName("appname")[0].firstChild.data.lower()
    properties = ["language", "appname", "licenseid", "licensedesc","url",
                  "desc", "demourl", "qsosappname", "qsosspecificformat"]
    for node in properties :
        try :
            header.appendChild(evaluation.firstChild.getElementsByTagName(node)[0])
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
    appfamilies = output.createElement("qsosappfamilies")
    appfamily = output.createElement("qsosappfamily")
    appfamily.appendChild(output.createTextNode(family))
    appfamilies.appendChild(appfamily)
    header.appendChild(appfamilies)
    
    #Write-out new evaluation sheet
    file = open(tmpDir + appname + "-" + release + ".qsos", 'w')
    file.write(output.toprettyxml('\t', '\n','utf-8'))
    file.close()

def UpgradeRepository(inDir, tmpDir):
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
    
    #Main loop : scan repository for app families
    for item in os.listdir(inDir):
        #Ignore CVS directory, includes directory and regular files
        if item != "CVS" and item != "include" and os.path.isdir(os.path.join(inDir, item)) :
            fitem = os.path.join(inDir, item)
            for evaluation in os.listdir(fitem) :
                #Ignore CVS directory
                eitem = os.path.join(fitem, evaluation)
                if evaluation != "CVS" and evaluation != "template" and evaluation != ".project" and os.path.isdir(eitem):
                    for sheet in os.listdir(eitem) :
                        if sheet != "CVS" :
                            sitem = os.path.join(eitem, sheet)
                            try :
                                UpgradeEvaluationSheet(sitem, item.title(), tmpDir)
                            except Exception, inst :
                                Errors.append("Error occured when upgrading " + filename + ":" + str(inst))
                            Upgraded += 1

    #Write a report on output
    report(Errors, Upgraded)
    
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
    
    #Setup git repository
    core.setup(Repo)
    
    #Main loop : prepare data for auto-commit and proceed
    for filename in os.listdir(inDir) :
        if filename.split(".")[ - 1] == "qsos" :
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
    
    #Write a report on output
    report(Errors, Submitted)


 