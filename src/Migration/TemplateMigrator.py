from xml.dom import minidom

import os

from Tools import FileObject
from Tools import report
from Tools import readAndCleanXML

def CreateEmptyFamily():
    family = minidom.Document()
    root = family.createElement("qsosappfamily")
    root.appendChild(family.createElement("include"))
    family.appendChild(root)
    return family.toprettyxml('\t','\n','utf-8')

def ReBuildFamilyTree(inTree, outDir):
    #Statistics vars
    Errors = []
    Upgraded = 0
    
    #Create a family template for each family folder
    for item in os.listdir(inTree) :
        if os.path.isdir(os.path.join(inTree, item)) :
            filename = os.path.join(outDir, item.title() + ".qtpl")
            try :
                file = open(filename, 'w')
                file.write(CreateEmptyFamily())
                file.close()
                Upgraded += 1
            except Exception, inst :
                Errors.append("Error occured when upgrading " + item + ":" + str(inst))

    #Print a Report
    report(Errors, Upgraded)