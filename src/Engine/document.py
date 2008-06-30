"""
Qsos evaluation's wrapper.

This module defines the document object.
"""
##
#    @author Hery Randriamanamihaga
#    @defgroup document Document
#    @ingroup Engine

from family import *

##
#    @ingroup document
#
class Document :
    """
    Python wrapper for QSOS document
    """
    
    def __init__(self, properties, families):
        """
        Initializer
        @param properties
            Document properties list
        @param families
            Application families list
        """
        self.properties = properties
        self.families = families
    
    def __getitem__(self,key):
        """
        Accessor
        
        This getter recognizes the following expressions :
            - properties
            - families
            - family.key
                <br>Where key can be :
                    - dateofcreation
                    - dateofvalidation
                    - authors
                    - [element].score
                    - [element].comments
        
        @param self
            Pointer to the object
        @param key
            The key to get
        @return
            The requested object
        """
        #Get properties of family and families sub components
        if key == "properties":
            return self.properties
        elif key == "families":
            return self.families
        elif key == "id":
            lang = self.properties['language']
            if lang == "en" :
                lang = ""
            else :
                lang = "_" + lang
            return self.properties['qsosappname'] + "-" + self.properties['release'] + lang 
        else :
            try :
                args = key.split(".") 
                return self.families[args[0]][".".join(args[1:])]
            except ValueError :
                return self.families[key]
            except KeyError :
                print "No key %s found in %s dictionnary of family %s" %        \
                         (args[1], args[2], args[0])
        
        
             
