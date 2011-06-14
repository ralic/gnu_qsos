#
#	Created by RPELISSE
#
#
#
#
#
module QuerySOS

require 'test/unit'
require 'querysos/CommandInterpreter'
require 'querysos/XMLCommandConfigurator'
require 'querysos/FileManager'
require 'querysos/Worker'

class TestCommandInterpreter < Test::Unit::TestCase

	attr_accessor	:interpreter
	def setup
		@interpreter = QuerySOS::CommandInterpreter.new
		@interpreter.configurator = QuerySOS::XMLCommandConfigurator.new
		w = QuerySOS::Worker.new
		w.filemanager= QuerySOS::FileManager.new
		@interpreter.worker = w
		@interpreter.init
	end

	def testOpen
#		@interpreter.current_cmd = 
	end

	def teardown

	end

end

end
