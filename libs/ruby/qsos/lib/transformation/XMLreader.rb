require 'rexml/document'
require 'model/element.rb'

module QSOS
class XMLreader

	attr	:properties
	
	def initialize()
		nbProps = 0
		@properties = Array.new
		@properties.push('comment')
        @properties.push('score')
        @properties.push('desc0')
        @properties.push('desc1')        
		@properties.push('desc2')
	end
	
	def extractValue(node)
		value = ""
		node.texts.each do |text|
			value += text.to_s
		end
		return value
#		set = node.find(node.name)
#		if set != nil && set.length > 0
#			return set.to_a.first.child
#		end
#		return ""
	end
	
	def extractAttribut(attribut)
		if attribut != nil 
			if attribut.class != REXML::Attribute
				# TODO : throw an exception
				puts "error"
			else
				return attribut.value
			end
		end
		return nil
	end
	
	#private:extractAttribut
	
    def makeElement(node)
    	if node.nil?
    		# TODO: throw an exception
    		return nil
    	end
        # creating an empty element
        item = QSOS::Element.new
        # element meta should be the name of the tag
        if node.name != nil && node.name != ""
        	item.meta = node.name
        else
        	item.meta = 'unkown'	# this is an error, should be checked... TODO:exception ?
        end
        # getting the text value
        value = ""
        node.texts.each do |text|
        	value += text.to_s
        end
        item.text = value
		# setting data from the node's attributes        
		item.title = extractAttribut(node.attribute('title'))
		item.name = extractAttribut(node.attribute('name'))
		# setting data form childs nodes	
		if node.has_elements?
			node.elements.each do |child|
	 			# the node is a property of this element
		 		if self.properties.include?(child.name)
		 			item.setMySelf(self.extractValue(child),child.name)
		 		else # the node is a children of this node
		 			item.childs.push(makeElement(child))
	 			end
		 	end
		end
		return item
	end
	
	def transformFrom(file_or_url)
		if file_or_url.nil? 
			return nil # TODO : throw an exception
		end
		# TODO : Check for URL	
		file = File.new(file_or_url)
		doc = REXML::Document.new(file)
    	return self.makeElement(doc.root)
	end
end
end
