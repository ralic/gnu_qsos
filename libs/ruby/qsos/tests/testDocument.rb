require	'test/unit'
require 'document'
class TestQSOSLib < Test::Unit::TestCase  
    
    def inititialize()
    end
    
    def setUp()
    end
    
    def testLib()
		lib = QSOS::Document.new 
		assert_not_nil(lib)
		lib.load("../tests/kollab.xml")
		
		data = "myComment"		
		element = "taskmanager"
		lib.setCommentByName(element,data)
		assert_equal(lib.getCommentByName(element),data);
		
		data = "2"
		lib.setScoreByName(element,data)
		assert_equal(lib.getScoreByName(element),data);
		
		
		first_author = "Goneri Le Bouder"
		second_author = "Romain PELISSE"
		authors = lib.getAuthors
		assert_equal(authors.size,2)
		assert_equal(authors[0],first_author)
		assert_equal(authors[1],second_author)
		
		third_author = "rpelisse"
		lib.addAuthor(third_author,"romain.pelisse@atosorigin.com")
		authors = lib.getAuthors
		assert_equal(authors.size,3)
		assert_equal(authors[0],first_author)
		assert_equal(authors[1],second_author)
		assert_equal(authors[2],third_author)
		
		lib.delAuthor(third_author)
		authors = lib.getAuthors
		assert_equal(authors.size,2)
		assert_equal(authors[0],first_author)
		assert_equal(authors[1],second_author)
		
		data = "bob"
		lib.setAppname(data)
		assert_equal(lib.getAppname(),data)
		
		data = "en"
		lib.setLanguage(data)
		assert_equal(lib.getLanguage(),data)
		data = "release"
		lib.setRelease(data)		
		assert_equal(lib.getRelease(),data)

#		Not implemented yet !		
#		data = "licencelist"
#		lib.setLicenselist(data)
#		assert_equal(lib.getLicenselist(""),data)
		puts "TODO: get/setLicenceList"
#		fail()
		
		data = "id"
		lib.setLicenseId(data)		
		assert_equal(lib.getLicenseId(),data)
	
		data = "license desc"
		lib.setLicenseDesc(data)		
		assert_equal(lib.getLicenseDesc(),data)
	
		data = "url"
		lib.setUrl(data)		
		assert_equal(lib.getUrl(),data)

		# TODO ! ! !
		# lib.getDesc("")
		# lib.setDesc("")	
		puts "TODO: get/setDesc"
#		fail()
		
		data = "demourl"
		lib.setDemoUrl(data)
		assert_equal(lib.getDemoUrl(),data)

		data = "qsos format"
		lib.setQsosformat(data)
		assert_equal(lib.getQsosformat(),data)

		data = "Qsosspecificformat"
		lib.setQsosspecificformat(data)
		assert_equal(lib.getQsosspecificformat(),data)

		data = "Qsosappfamily"
		lib.setQsosappfamily(data)
		assert_equal(lib.getQsosappfamily(),data)
		lib.write('./output.xml')
	end
end
