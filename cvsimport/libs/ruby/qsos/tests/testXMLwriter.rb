require 'test/unit'
require 'transformation/XMLreader.rb'
require 'transformation/XMLwriter.rb'
require 'model/element.rb'
require '../tests/testXMLreader.rb'

class TestXMLWriter < TestXMLReader

	def initialize(arg)
		super(arg)				
		@reader = QSOS::XMLreader.new()
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

	def testXMLwriter
		outputFilename = '../tests/qsos.xml'
		node = @reader.transformFrom('../tests/kollab.xml')
		sheet = Sheet.new(node)
		document = QSOS::XMLWriter.new().xmlize(sheet)
		document.write(File.new(outputFilename,"w+"))		
		# checking if the file has been created
		begin 
			product = File.open(outputFilename,File::RDONLY)
			# TODO: Add some integrity tests to check ouput file
			# clean : we remove the file
			File.delete(outputFilename)
		rescue Errno::ENOENT
			# the file does not exist ! the test failed
			fail
		ensure
			puts 'ensure'
			product.close if product and ! product.closed?
		end
	end

    def testMakeElement
        item = @reader.makeElement(node)
        assert_not_nil(item)
        assert_instance_of(QSOS::Element, item)
        # TODO: do more test here !
        assert_equal(item.text,@TEXT)
        assert_equal(item.name,@NAME)
        assert_equal(item.desc0,@DESC0)
	    assert_equal(item.desc1,@DESC1)
	    assert_equal(item.desc2,@DESC2)
        assert_equal(item.comment,@COMMENT)
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
