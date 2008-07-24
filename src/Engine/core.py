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
        Path to root of repository local copy.
        Default value is ..
    
    @todo Scan repository and build tree on initialization?
    """
    global PATH
    global STORE
    
    PATH = path
    STORE = {}

    
def submit(qsos):
    
    """
    Submit an evaluation.
    
    Parse, validate, split and store .qsos XML file to repository as .qscore
    files. Files in repository are overwritten without confirmation and objects
    contained in STORE dictionnary are updated.
    
    @param qsos
        Evaluation to submit
    """
    #Unpack raw XML qsos evaluation into single-lined string
    evaluation = "".join(line.strip() for line in qsos.readlines())
    
    #Create document object and add/update it in STORE
    #Key of STORE's item is appname-version_language
    document = splitter.createDocument(evaluation,
                                       os.path.join(PATH,"sheets","families"))
    STORE[document["id"]] = document
    
    #Generate .qscore files into repository
    scores = splitter.parse(document, PATH)
    
    for file in scores :
        REPO = git.open('core', os.path.join(PATH, ".git"))
        REPO.git('push')
        REPO[file] = scores[file]
        REPO.commit(file + " generated")
        REPO.git('push')
        REPO.close()


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
    if evaluation not in STORE :
        STORE[evaluation] = builder.build(evaluation, PATH)
    sheet = builder.assembleSheet(STORE[evaluation], PATH)
    return builder.fillSheet(STORE[evaluation], sheet, PATH)
