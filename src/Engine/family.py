"""
Family evaluation's wrapper.

This module defines the family object.
"""
##
#    @author Hery Randriamanamihaga
#    @defgroup family Family
#    @ingroup Engine

    ##
    #    @ingroup family
    #
class family:
    """
    QSOS Application family wrapper for QSOS
    
    This class provides a wrapper for a family evaluation which corresponds to
    qscore files stored on the repository
    """
    
    
    def __init__(self,author, scores,comments):
        """
        Initializer
        
        Creates a new family object with provided parameters
        
        @param authors
                A list of family's evaluation authors.
                Each entry is a couple of string (name, e-mail)
        @param scores
                Scores' dictionnary
                Key entry is the element id from qsos xml
        @param comments
                Comments' dictionnary
                Key entry is the element id from qsos xml
        """
        self.author = author
        self.scores = scores
        self.comments = comments
    
    def __getitem__(self, item) :
        """
        Accessor
        
        Getter
        
        @param self
                Pointer to the object
        @param item
                The item to get from this family. It can be :
                    - dateofcreation
                    - dateofvalidation
                    - authors
                    - element.score
                    - element.comment
            
        """
        #First case : Authors
        if item == "author" :
            return self.author
        #Default case : any item
        else :
            [key, what] = item.split(".")
            if what == "score" :
                return self.scores[key]
            elif what == "comment" :
                return self.comments[key]