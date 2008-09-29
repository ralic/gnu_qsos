"""
QSOS-Engine's core module.

This module provides should schedule Engine's task. The current implementation
does not uses it yet.
"""

##
#    @defgroup core Core 
#    @ingroup Engine
#    @author Hery Randriamanamihaga


from Engine import document, splitter, builder

import os
import re
from Repository import gitshelve as git

##
#    @ingroup core
#
"""
QSOS engine core application.

Main application that runs the engine and performs required task for each
QSOS-Engine's feature.
"""

def setup(path=".."):
    """
    Initializer
    
    Initializes global variables such as path to root of repository or object
    collection in memory
        
    @param path
        Path to root of local repository.
        Default value is ..
    
    @todo Scan repository and build tree on initialization?
    """
    global PATH
    global STORE
    
    PATH = path
    os.chdir(PATH)

    
def submit(data):#, author, email, comment):
    """
    Submit an evaluation.
    
    Parse, validate, split and store .qsos XML file to repository as .qscore
    files. Files in repository are overwritten without confirmation and objects
    contained in STORE dictionnary are updated.
    
    @param data
        Data to submit. data must be a dictionnary with the following keys:
            * Author
            * E-mail
            * File type (evaluation, template or family)
            * Description of uploaded file
            * File
    """
    #First check if e-mail adress is valid and format git author
    if checkEmail(data["E-mail"]):
        Author = "%s <%s>" % (data["Author"], data["E-mail"])
    else :
        raise StandardError("Not a valid e-mail adress")
    
    #Open and update the repository
    repository = git.open('Migration', os.path.join(PATH, ".git"))
    repository.git('pull')
    
    #Put contribution on the correct location
    if data["Type"] == "Evaluation" :
        #Unpack raw XML qsos evaluation into single-lined string
        evaluation = "".join(line.strip() for line in data['File'].file.readlines())
        
        #Create document object and add/update it in STORE
        #Key of STORE's item is appname-version_language
        document = splitter.createDocument(evaluation,
                                           os.path.join(PATH,"sheets"))
        
        #Generate .qscore files into repository
        scores = splitter.parse(document, PATH)
        
        for file in scores :
            repository[file] = scores[file]
    
    #Make a commit with proper parameters
    repository.commit("%s added %s into %s.\n%s" % (Author, data['File'].filename, data['Type'], data['Description']),
                      (data['Author'], data['E-mail'])
                    )
    repository.git('push')
    repository.close()     
       
def show (str):
    print str
    
def checkEmail (str):
    return re.compile("\S+@\S+").match(str)


def request(evaluation):
    """
    Assemble and return a qsos XML file from repository.
    
    A document object copy of the evaluation is first checked into STORE
    before building it from filesystem.
    
    STORE attribute of the object is supposed to be up to date, otherwise,
    the returned evaluation can be out of date.
    
    When the document representation of the requested evaluation is not yet 
    into STORE, it will be builded from repository if possible. 
    
    @param evaluation
        Requested evaluation's id. The id must be qsosappname-release
    """
    eval = builder.build(evaluation, PATH)
    sheet = builder.assembleSheet(eval, PATH)
    return builder.fillSheet(eval, sheet, PATH)
