#!/usr/bin/ruby -w
require 'rexml/document'
require 'model/element.rb'
require 'model/sheet.rb'

module QSOS
class XMLWriter

	def initialize()
  	end
  
	def xmlize(sheet)
		@doc = REXML::Document.new()
		@doc.add_element( self.transformAnElement(sheet.root) )
		return @doc
	end
   
	def transformAnElement(item)
      	# Creating the node itself
      	if item == nil
      		return nil
      	end
      	# 
      	if item.meta != nil
	    	node = REXML::Element.new(item.meta)
	    else
	    	return nil	# TODO: this is an error, throw an exception ? 
	    end
		# setting proper attribut for this element
    	if  item.name != nil && item.name != ""
  	  		node.add_attribute('name',item.name)
    	end
  		
    	if  item.title != nil
    		node.add_attribute('title',item.title)
		end
  	  	# Adding the element text 
  	  	if item.text != nil && item.text != ""
    		node.add_text(item.text)
    	end 
  	  	# TODO: use getMySelf ( to rename in getProperty) to improve the next operation
    	if item.desc != nil  
    		elchild = REXML::Element.new("desc")
    		elchild.add_text(item.desc())
    		node.add_element(elchild)
    	end
    		
		if item.desc0 != nil
    		elchild = REXML::Element.new("desc0")
			elchild.add_text(item.desc0)
			node.add_element(elchild)
		end
    	
    	if item.desc1 != nil
    		elchild = REXML::Element.new("desc1")
    		elchild.add_text(item.desc1)
    		node.add_element(elchild)
    		
    	end
    	
    	if item.desc2 != nil
    		elchild = REXML::Element.new("desc2")
    		elchild.add_text(item.desc2)
			node.add_element(elchild)
    	end
    	
    	if 	item.comment != nil && item.comment != ""
    		elchild = REXML::Element.new("comment")
    		elchild.add_text(item.comment)
    		node.add_element(elchild)
    	end
    	
    	if item.score != nil && item.score != ""
    		elchild = REXML::Element.new("score")
    		elchild.add_text(item.score)
			node.add_element(elchild)
    	end	
    	
    	# Adding every child to the node
    	nbChild = -1
    	while nbChild < item.childs.length
    		child = self.transformAnElement( item.childs[nbChild += 1] )
    		if ! child.nil? 	
	    		node.add_element(child )
	    	end
    	end		
    	return node
	end
end
end