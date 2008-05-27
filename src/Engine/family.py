class family:
    "QSOS Application family wrapper for QSOS"
    def __init__(self,authors, dates, scores,comments):
        """Create new family object
        
        Parameters :
            * authors   : [(string,string)] - couples (name,email)
            * dates     :  (string,string)  - couple (creation, validation)
            * scores    :  {string:string}  - dictionary of name:value
            * comments  :  {string:string}  - dictionary of name:content"""
            
        self.authors = authors
        self.dates = dates
        self.scores = scores
        self.comments = comments
        
    def __getitem__(self, item) :
        """Getter
        
        Parameters :
            * item  : string    -   the item to get from self object
        item parameter content must be  :
            * date.value with value as creation or validation
            * authors
            * key.what with what as score or comment"""
        
        #First case : dates
        if item.startswith("date") :
            if item.endswith("creation") :
                return self.dates[0]
            else :
                return self.dates[1]
        #Second case : authors
        elif item == "authors" :
            return self.authors
        #Default case : any item
        else :
            [key, what] = item.split(".")
            if what == "score" :
                return self.scores[key]
            elif what == "comment" :
                return self.comments[key]