class family:
    "QSOS Application family wrapper for QSOS"
    def __init__(self,authors, dates, scores={},comments={}):
        self.authors = authors
        self.dates = dates
        self.scores = scores
        self.comments = comments
        
    def __getitem__(self, item) :
        if item.startswith("date") :
            if item.endswith("creation") :
                return self.dates[0]
            else :
                return self.dates[1]
        elif item == "authors" :
            return self.authors
        else :
            [key, what] = item.split(".")
            if what == "score" :
                return self.scores[key]
            elif what == "comment" :
                return self.comments[key]