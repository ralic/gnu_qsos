from Engine import document
from Engine import family
from xml.dom import minidom
import os

def build(name, version, repositoryroot="../.."):
    #Read the template of requested evaluation from repository
    template = "-".join([name,version])
    template = ".".join([template,"qtpl"])
    template = os.path.join(repositoryroot,"sheets","templates",template)
    template = file(template).read()
    template = "".join([line.strip() for line in (template.splitlines())])
    
    #Extract template contents
    content = minidom.parseString(template).firstChild.lastChild.childNodes
    
    #Extract properties from template contents
    properties = [node.firstChild.data for node in content[0:-2]]
    
    #Build families object according to families declared from template
    includes = [node.firstChild.data for node in content[-1].childNodes]
    includes.insert(0,"generic")
    families ={}
    for include in includes :
        familySheet = ".".join([include,"qscore"])
        familySheet = os.path.join(repositoryroot,"sheets","evaluations",name,version,familySheet)
        familySheet = file(familySheet).read()
        familySheet = "".join([line.strip() for line in familySheet.splitlines()])
        xml = minidom.parseString(familySheet).firstChild
        
        [h, elt] = xml.childNodes
        [a,d]=h.childNodes
        
        authors =[(n.firstChild.data,e.firstChild.data) 
                  for [n,e] in [i.childNodes for i in a.childNodes]]
        
        value = lambda x : (x and [x.data] or [""])[0]
        dates = (value(d.firstChild.firstChild),value(d.lastChild.firstChild))
        
        scores = {}
        comments = {}
        for node in elt.childNodes :
            n = node.getAttribute("name")
            v = node.getElementsByTagName("score")
            if v : scores[n] = v[0].firstChild.data 
            v = node.getElementsByTagName("comment")
            if v : comments[n] = v[0].firstChild.data 
        families[include]=family.family(authors, dates, scores, comments)
    return document.Document(properties,families)
    
    
def assembleSheet(name, repositoryroot="../.."):
    pass

def toqsos(sheet, document):
    pass