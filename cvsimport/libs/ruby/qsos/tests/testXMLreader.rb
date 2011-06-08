require 'test/unit'
require 'transformation/XMLreader.rb'
require 'transformation/XMLwriter.rb'
require 'model/element.rb'

class TestXMLReader < Test::Unit::TestCase

	attr	:writer
	attr	:reader
	attr	:node

	def initialize(arg)
		super(arg)
		@writer = QSOS::XMLWriter.new()
		@reader = QSOS::XMLreader.new()
		
		@META = "element"
		@NAME = "MyNAME"
		@TEXT = "MyTEXT"
		@COMMENT = "MyCOMMENT"
		@SCORE = "0"
		@DESC0 = "desc zero"
		@DESC1 = "desc one"
		@DESC2 = "desc two"
				
	end

	def setup
	    original = QSOS::Element.new(@NAME,@SCORE,@COMMENT,@TEXT,@META)
		original.desc0 = @DESC0
		original.desc1 = @DESC1
		original.desc2 = @DESC2
		
		elementChild = QSOS::Element.new(name="child",score="0",comment="ChildComment",text="CHILD",meta="element")
		elementChild.desc0 = "desc zero"
		elementChild.desc1 = "desc one"
		elementChild.desc2 = "desc two"
		original.childs.push(elementChild)
		
		elementChild2 = QSOS::Element.new(name="child",score="0",comment="ChildComment",text="CHILD",meta="element")
		elementChild2.desc0 = "desc zero"
		elementChild2.desc1 = "desc one"
		elementChild2.desc2 = "desc two"
		original.childs.push(elementChild2) 
		@node = writer.transformAnElement(original) 
    end

    def testMakeElement
        item = @reader.makeElement(node)
        puts item.inspect
        assert_not_nil(item)
        assert_instance_of(QSOS::Element, item)
        # TODO: do more test here !
        assert_equal(@TEXT,item.text)
        assert_equal(@NAME,item.name)
        assert_equal(@DESC0,item.desc0)
	    assert_equal(@DESC1,item.desc1)
	    assert_equal(@DESC2,item.desc2)
        assert_equal(@COMMENT,item.comment)
	end

	def testTransform
		node = @reader.transformFrom('../tests/kollab.xml')
		assert_not_nil(node)
		assert_instance_of(QSOS::Element,node)
		# TODO: Add a lot more assertions ! ! !
	end

    def teardown
    end	
end
