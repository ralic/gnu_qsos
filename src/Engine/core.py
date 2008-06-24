"""
QSOS-Engine's core module.

This module provides should schedule Engine's task. The current implementation
does not uses it yet.
"""

##
#    @defgroup core Core 
#    @ingroup Engine
#    @author Hery Randriamanamihaga


from Engine import document
from Engine import splitter
from Engine import builder
from os import path

##
#    @ingroup core
#
class Core():
    """
    QSOS engine core application.
    
    Main application that runs the engine and performs required task for each
    QSOS-Engine's feature.
    """
    
    def __init__(self, path=".."):
        """
        Initializer
        
        Attributes :
            - library -- Dictionnary of evaluation stored on the local repository
            - path -- Path to local repository
            
        @param path
            Path to root of repository local copy.
            Default value is ..
        
        @todo Scan repository and build tree on initialization?
        """
        self.library = {}
        self.path = path
    
    def submit(self, qsos):
        """
        Submit an evaluation.
        
        Parse, validate, split and store .qsos XML file to repository as .qscore
        files. Files in repository are overwritten without confirmation and objects
        contained in library dictionnary are updated.
        
        @param qsos
            Evaluation to submit
            
        @attention
            Not yet tested
        """
        #Unpack raw XML qsos evaluation into single-lined string
        evaluation = "".join(line.strip() for line in qsos.read().splitlines())
        
        #Create document object and add/update it in library
        #Key of library's item is appname-version_language
        document = splitter.createDocument(evaluation,
                                           path.join(self.path,sheets,family))
        self.library[document["id"]] = document
        
        #Generate .qscore files into repository 
        splitter.parse(document, self.path)

    
    def request(evaluation):
        """
        Assemble and return a qsos XML file from repository.
        
        A document object copy of the evaluation is first checked into library
        before building it from filesystem.
        
        library attribute of the object is supposed to be up to date, otherwise,
        the returned evaluation can be out of date.
        
        When the document representation of the requested evaluation is not yet 
        into library, it will be builded from repository if possible. 
        
        @param evaluation
            Requested evaluation's id. The id must respect the following format:
            qsosappname-release_language or qsosappname-release when evaluation's
            language is english
            
        @attention
            Not yet implemented
        """
        try :
            builder.assembleSheet(library[evaluation])
        except KeyError :
            pass