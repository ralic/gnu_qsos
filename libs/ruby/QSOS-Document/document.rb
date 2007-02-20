require 'qsos/model/sheet.rb'
require 'qsos/model/element.rb'
require 'qsos/model/searchQuery.rb'

require 'qsos/transformation/XMLreader.rb'
require 'qsos/transformation/XMLwriter.rb'

module QSOS
class Document

	attr	:sheet

	#
	#	This convenient constructor allow to build a QSOSLib object
	#	already with a specified file ( or a specified url).
	#
    def initialize(url=nil)
    	if ! url.nil?
    		self.load(url)
    	end
    	@sheet = Sheet.new
    	
    	@QSOS_SPECIFIC_FORMAT = "qsosspecificformat"
    	@QSOS_APP_FAMILY = "qsosappfamily"
    	@QSOS_FORMAT = "qsosformat"
    	@DEMO_URL = "demourl"
    	@APPNAME = "appname"
    	@TEXT = "text"
    	@COMMENT = "comment"
    	@AUTHORS = "authors"
    	@NAME = "name"
    	@EMAIL = "email"
    	@SCORE = "score"
    	@RELEASE = "release"
    	@LICENCE_ID = "licenseid"
    	@LICENCE_LIST = ""
    	
    end
    
    # a search method
    def search(search)
    	if search.nil?
    		# TODO: Throw an exception
    	end
    	if search.class != SearchQuery
    		#TODO: Throw an exception 
    	end	
    	results = Array.new
    	match?(search.name,@sheet.root,results)
    	return results
    end 
    private:search
    
    #
    #	This method allow you to parse the elements tree to find
    #	a list of elements matching the 'name'. This is a recursive
    #	method, an the first call, be sure to pass the sheet's root
    #	as an element to be sure to parse the entire sheet.
    #	You may or may not initialize the 'results' Array. If you do
    #	not it'll be initialize, be sure NOT to pass an already existing
    #	array ( or be sure of what you're doing...)
    #
    #	@param name, name of the element search for
    #	@param element, the current element searched
    #	@results list of matching elements
    #	@return none
    #
    def match?(name,element,results=Array.new)
    	if 	(! name.nil?) && (! element.nil?) && element.class == Element 
    		if results.nil?
    			results = Array.new
    		end
    		if element.name == name || element.meta == name
    			results.push(element)
    		end
			nbChild = -1
			while nbChild < element.childs.length
				match?(name,element.childs[nbChild += 1],results)
			end	
    	else
    		# TODO : Throw an exception
    	end
    end
    private:match?
	
	public    
    def load(url=nil)
    	# TODO : check that url is a valid url
    	if url.nil?
    		# TODO: throw an exception
    	end
    	reader = XMLreader.new
    	@sheet.root = reader.transformFrom(url)
    end
	
	 
	 # Allows to get the description number numDesc of the element called name.
	 #
	 # @param name the name to search.
	 # @param numDesc int representing the number of the description to search.
	 # @return a  corresponding to the description asked.
	 #/
	 def getDescByName(name="",numDesc=0)
	 	query = SearchQuery.new
	 	query.name = name
		return self.search(query)[0].getMySelf("desc"+numDesc)
     end
	 # Allows to set a comment to an element given by his name.
	 # 
	 # @param name the name of the element.
	 # @param comment the comment to set.
	 #/
	 def setCommentByName(name,comment)
 	 	query = SearchQuery.new
	 	query.name = name
		item = search(query)[0]
		if ! item.nil? 
			item.setMySelf(comment,@COMMENT)
		end
	 end
	
	 # Allows to get the comment on an element.
	 # 
	 # @param name the name of the element to get.
	 # @return a  corresponding to the comment of the element asked.
	 #/
	 def getCommentByName(name)
	  	query = SearchQuery.new
	 	query.name = name
		item = search(query)[0]
		if ! item.nil?
			item.getMySelf(@COMMENT)
		end 
	 end
	 # Allows to get the score of an element.
	 # 
	 # @param name the name of the element.
	 # @return a  representing the score of the Element.
	 #/
	def getScoreByName(name)
	 	query = SearchQuery.new
	 	query.name = name
		item = search(query)[0]
		if ! item.nil?
			return item.getMySelf(@SCORE) 	
		end
		return nil
	end
	#Allows to set the score of an element
	 # 
	 # @param name the name of the element.
	 # @param score a  representing the score to set.
	 #/
	def setScoreByName(name,score)
		query = SearchQuery.new
	 	query.name = name
	 	item = search(query)[0]
	 	if ! item.nil?
	 		item.setMySelf(score,@SCORE) 	
	 	end
	end
	#
	 # Allows to get the name of all the authors.
	 # 
	 # @return an Array that contains the names of all the authors.
	 #/
	def getAuthors()
		names = Array.new
		query = SearchQuery.new
	 	query.name = "author"
	 	authors = search(query)
	 	nbAuthor = -1
	 	while nbAuthor < authors.length
	 		names.push(authors[nbAuthor += 1].text)
	 	end
		return names 	
	end
	
	#	Allows to add an author to the list of authors.
	# 
	# @param name the name of the author to add.
	# @param email the email of the author to add.
	#
	def addAuthor(author_name,author_email) 
		if (! author_name.nil?) && (! author_email.nil?)
			query = SearchQuery.new
		 	query.name = @AUTHORS
		 	author = Element.new("","","","",@AUTHORS)
		 	name = Element.new("","","",author_name,@NAME)
		 	author.childs.push(name)
		 	email = Element.new("","","",author_email,@EMAIL)
		 	author.childs.push(email)
		 	authors = search(query)
		 	if ! authors[0].nil?
		 		authors[0].childs.push(author)
		 	end
		 else
		 	# TODO : throw an exception
		 end
    end
	 # Allows to delete an author.
	 # 
	 # @param name the name of the author to delete.
	 #
	def delAuthor(name) 
		query = SearchQuery.new
	 	query.name = @AUTHORS
		authors = search(query)
		
    end
		
	 #Allows to get the application name.
	 # 
	 # @return a  corresponding to the application name.
	 #/
	def getAppname()
		query = SearchQuery.new
	 	query.name = @APPNAME
	 	item = search(query)[0]
		if ! item.nil?
			return item.getMySelf(@TEXT)
	 	end
	end
	
	#
	#	Allows to set the application name.
	# 
	# 
	#	@param appname the application name to set.
	#
	def setAppname(appname)
		query = SearchQuery.new
	 	query.name = @APPNAME
		item = search(query)[0]
		if ! item.nil?
			item.text = appname
		else
			# throw an exception
			
		end
	end
	
	#
	#	Allows to get the language.
	# 
	# @return a  corresponding to the language.
	#
	def getLanguage()
		query = SearchQuery.new
	 	query.name = "language"
		item = search(query)[0]
		if ! item.nil? 
			return item.getMySelf(@TEXT)
		else
			# trow an exception
		end
	end
	
	#
	#	Allows to set the language.
	# 
	# 
	#	@param language the language to set.
	#
	def setLanguage(language)
		query = SearchQuery.new
	 	query.name = "language"
		item = search(query)[0]
		if ! item.nil?
			item.text = language	
    	end
	end
	
	#
	# Allows to get the release number.
	# 
	# @return a  corresponding to the release number.
	#/
	def getRelease()
		query = SearchQuery.new
	 	query.name = @RELEASE
		item = search(query)[0]
		if ! item.nil?
			return item.text
		else
			# throw an exception
		end
		
	end
	#Allows to set the release.
	# 
	# 
	# @param release the release to set.
	#/

	def setRelease(release)
		query = SearchQuery.new
	 	query.name = @RELEASE
		item = search(query)[0]
		if ! item.nil?
			item.text = release
		end
	end
	# 
	#	Not implemented yet 
	#/
	def getLicenselist()
		query = SearchQuery.new
	 	query.name = @LICENCE_LIST
		item = search(query)[0]
		if ! item.nil?
			return item.text
		else
			# thow an exception
		end
    end
    
	#
	#	Allows to get the license Id.
	# 
	#	@return a  corresponding to the License Id.
	#
	def getLicenseId()
		query = SearchQuery.new
	 	query.name = @LICENCE_ID
		item = search(query)[0]
		if ! item.nil?
			return item.text
		end
	end
    
    #
    #	Allows to set the license Id.
	# 
	# 
	#	@param licenseId, the license Id to set.
	#
	def setLicenseId(licenseId)
		query = SearchQuery.new
	 	query.name = @LICENCE_ID
		item = search(query)[0]
		if ! item.nil? 
			item.text = licenseId
		end
	end
	
	#
 	#	Allows to get the license Description.
	# 
	#	@return a  corresponding to the license Description.
	#
	def getLicenseDesc()
		query = SearchQuery.new
	 	query.name = @LICENCE_ID
		item = search(query)[0]
		if ! item.nil?
			return item.text
		else
			# TODO: throw an exception
		end		
	end
	
	#
	#	Allows to set the license Description.
	# 
	# 
	# 	@param licensedesc the license Description to set.
	#
	def setLicenseDesc(licensedesc)
		query = SearchQuery.new
	 	query.name = @LICENCE_ID
		item = search(query)[0]
		if ! item.nil?
			item.text = licensedesc
		else
			# TODO: throw an exception
		end	
	end
	
	#
	#	Allows to get the url.
	# 
	#	@return a  corresponding to the url.
	#
	def getUrl()
		query = SearchQuery.new
	 	query.name = @DEMO_URL
		item = search(query)[0]
		if ! item.nil?
			return item.text
		end
	end
	
	#
	#	Allows to set the url.
	# 
	# 	@param url the url to set.
	#
	def setUrl(url)
		query = SearchQuery.new
	 	query.name = @DEMO_URL
		item = search(query)[0]
		if ! item.nil?
			item.text = url
		else
			# TODO: throw an exception
		end
	end
	
	#
	#	Allows to get the description.
	# 
	#	@return a  corresponding to the description.
	#
	# TODO : Attention ! à Implémenter
	def getDesc()
		query = SearchQuery.new
	 	query.name = ""
		item = search(query)[0]
		if ! item.nil?
			return item.desc
		else
			# TODO: Thow an exception
		end
	end
	#Allows to set the description.
	# 
	# 
	# @param desc the description to set.
	#
	# TODO : Attention ! à Implémenter
	def setDesc(desc)
		query = SearchQuery.new
	 	query.name = ""
		item = search(query)[0]
		if ! item.nil?
			item.desc = desc
		else
			# TODO : Throw an...
		end
	end
	
	#
	#	Allows to get the demonstration url.
	# 
	#	@return a  corresponding to the demonstration url.
	#
	def getDemoUrl()
		query = SearchQuery.new
	 	query.name = @DEMO_URL
		item = search(query)[0]
		if ! item.nil?
			return item.text
		end
	end
	
	#
	#	Allows to set the demonstration url.
	#  
	#	@param demourl the demonstration url to set.
	#
	def setDemoUrl(demourl)
		query = SearchQuery.new
	 	query.name = @DEMO_URL
		item = search(query)[0]
		if ! item.nil?
			item.text = demourl
		else
			# TODO : Throw an exception
		end
	end

	#
	#	Allows to get the QSOS format.
	# 
	#	@return a  corresponding to the QSOS format.
	#
	def getQsosformat()
		query = SearchQuery.new
	 	query.name = @QSOS_FORMAT
		item = search(query)[0]
		if ! item.nil?
			return item.text
		else
			# TODO: exception
		end
	end
	
	#
	#	Allows to set the QSOS format.
	# 
	# 
	#	@param qsosformat the QSOS format to set.
	#
	def setQsosformat(qsosformat)
		query = SearchQuery.new
	 	query.name = @QSOS_FORMAT
		item = search(query)[0]
		if ! item.nil? 
			item.text = qsosformat
		else
			# TODO : exception
		end
	end
	#Allows to get the QSOS specific format.
	 # 
	 # @return a  corresponding to the QSOS specific format.
	 #/
	def getQsosspecificformat()
		query = SearchQuery.new
	 	query.name = @QSOS_SPECIFIC_FORMAT
		item = search(query)[0]
		if ! item.nil?
			return item.text
		else
			# TODO : exception
		end
	end
	#Allows to set the QSOS specific format.
	 # 
	 # 
	 # @param qsosspecificformat the QSOS specific format to set.
	 #/

	def setQsosspecificformat(qsosspecificformat)
		query = SearchQuery.new
	 	query.name = @QSOS_SPECIFIC_FORMAT
		item = search(query)[0]
		if ! item.nil?
			item.text = qsosspecificformat
		else
			# TODO : throw an exception
		end
	end
	
	#
	#	Allows to get the application family in QSOS.
	# 
	#	@return a  corresponding to the application family in QSOS.
	#
	def getQsosappfamily()
		query = SearchQuery.new
	 	query.name = @QSOS_APP_FAMILY
		item = search(query)[0]
		if ! item.nil?
			return item.text
		end
	end
	
	#
	#	Allows to set the application family in QSOS.
	# 
	# 
	#	@param qsosappfamily the application family in QSOS to set.
	#
	def setQsosappfamily(qsosappfamily)
		query = SearchQuery.new
	 	query.name = @QSOS_APP_FAMILY
		item = search(query)[0]
		if ! item.nil?
			item.text = qsosappfamily
		else
			# TODO: exception
		end

		
	#	Allows to get the application name as it stored in the 
	#	sheet name.
	# 
	#	@return a  corresponding to the application family in QSOS.
	#
	def getQsosappname()
		query = SearchQuery.new
	 	query.name = @QSOS_APP_FAMILY
		item = search(query)[0]
		if ! item.nil?
			return item.text
		end
	end
	
	#
	#	Allows to set the application name as it stored in the 
	#	sheet name.
	# 
	#	@param qsosappname the application family in QSOS to set.
	#
	def setQsosappname(qsosappname)
		query = SearchQuery.new
	 	query.name = @QSOS_APP_FAMILY
		item = search(query)[0]
		if ! item.nil?
			item.text = qsosappname
		else
			# TODO: exception
		end

    end
    
    #
	#	Allows to write the xml file at the given path. 
	#	This method has a problem since it degrated the xml file (
	#	not the datas but the presentation).
	#	It will be fixed in the next version
	# 
	#	@param path
	#
	def write(path)
		file = File.open(path,"w")
		if ! file.nil?
			writer = XMLWriter.new
			file << writer.xmlize(@sheet)
		else
			# TODO
			puts "todo !!!"
		end
	end
end
end
