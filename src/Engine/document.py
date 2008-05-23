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
        if key == "properties":
            return self.properties
        else :
            try :
                [family,criterion]=key.split(".")
                return self.families[family][criterion]
            except ValueError :
                return self.families[key]
        
        
             
