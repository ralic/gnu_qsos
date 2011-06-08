#
#	Created by RPELISSE
#
#
#
#
#
module QuerySOS
require 'test/unit'
require 'querysos/XMLCommandConfigurator'

class TestXMLCommandConfigurator < Test::Unit::TestCase

	def testParsingOneCommand
		data = "./tests/commandConf.xml"
		configurator = QuerySOS::XMLCommandConfigurator.new
		configurator.configFilePath= data
		command = configurator.load
		assert_not_nil command
		puts command.inspect
		assert_equal(Array,command.class)
		assert_equal(1,command.size)
		assert_equal(QuerySOS::Command,command[0].class)
		assert_equal("o",command[0].id)
		assert_equal("open",command[0].name)
		assert_equal("1",command[0].minArgs)
		assert_equal("1",command[0].maxArgs)
	end

end

end