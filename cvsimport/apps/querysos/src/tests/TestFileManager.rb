require 'test/unit'
require 'querysos/FileManager'

class TestFileManager < Test::Unit::TestCase

	attr_reader	:FILENAME
	attr_reader	:manager

	def initialize(arg)
		super arg
		@FILENAME = "./tests/kollab.xml"
		@manager = nil
	end

	def setup
		@manager = QuerySOS::FileManager.new	
		assert_not_nil @manager		
	end

	def testOpenWithNoFile
		# trying to open an empty file
		begin
			@manager.open ""
		rescue 
			# exception is thrown : good behavior
			return
		end
		fail
	end

	def testValidOpen
		# opening an correct file, no exception thrown
		@manager.open @FILENAME
		assert_not_nil @manager.filename
		assert_equal(@FILENAME,@manager.filename)
		assert_not_nil @manager.current_sheet 
	end

	def testInvalidClose
		begin
			@manager.close
		rescue 
			# good behavior
			return
		end
		fail
	end

	def testValidClose
		@manager.open @FILENAME
		@manager.close
		assert_not_nil @manager.filename
		assert_equal("",@manager.filename)
		assert_nil @manager.current_sheet
	end

	def testSave
		@manager.open @FILENAME
		@manager.save
	end

	def teardown
		if @manager.current_sheet != nil
			@manager.close
		end
	end

end