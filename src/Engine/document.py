from family import *

class Document :
    "Python wrapper for QSOS document"
    def __init__(self, properties, families):
        """Initializes the document with :
        - document properties
        - application families"""
        self.properties = properties
        self.families = families
    
    def __getitem__(self,key):
        #Get properties of family and families sub components
        if key == "properties":
            return self.properties
        else :
            try :
                args = key.split(".") 
                return self.families[args[0]][".".join(args[1:])]
            except ValueError :
                return self.families[key]
            except KeyError :
                print "No key %s found in %s dictionnary of family %s" % (args[1], args[2], args[0])
        
        
             
