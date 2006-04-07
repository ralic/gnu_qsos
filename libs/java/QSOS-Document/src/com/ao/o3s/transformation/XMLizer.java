/* $Id: XMLizer.java,v 1.1 2006/04/07 12:03:39 aclerf Exp $
*
*  Copyright (C) 2006 Atos Origin 
*
*  Author: Arthur Clerfeuille <arthur.clerfeuille@atosorigin.com>
*
*  This program is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  You should have received a copy of the GNU General Public License
*  along with this program; if not, write to the Free Software
*  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

package com.ao.o3s.transformation;

import java.io.ByteArrayOutputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.io.PrintStream;

import org.jdom.Document;
import org.jdom.Element;
import org.jdom.output.XMLOutputter;

import com.ao.o3s.data.IElement;
import com.ao.o3s.data.ISheet;
/**<p>
 * The aim of this class is to do the conversion into a xml
 * document of the java modelisation. In order to do the conversion,
 * the library jdom is used.
 * 
 * @author rpelisse 
 * @author aclerf
 */

public class XMLizer implements IXMLizer
{	
	/**
	 * This method takes an ISheet, representing the
	 * xml file and convert it into a string which is the content
	 * of an xml file. 
	 * 
	 *  @param sheet the sheet in the java modelisation.
	 *  @return String the string representation of the xml.
	 * 
	 */	
	public String transformToXml(ISheet sheet) 
	{
		IElement target = sheet.getRoot();
		if (target !=null)
			{
				Document doc = new Document();
				doc.setRootElement(transformAnIElement(target));
				return docToString(doc);
			}
		return "";
	}
	
	/**
	 * This method takes a Sheet and create the xml file given
	 * by the path 
	 * 
	 *  @param target the sheet in the java modelisation.
	 *  @param path the path of the file to create.
	 * 
	 */	
	public void transformToXml(ISheet target,String path){
		String xml = transformToXml(target);
		createFile(xml,path);
		}

	/**
	 * This method converts a document (org.jdom.Document)
	 * into a String.
	 * 
	 *  @param doc the document to convert.
	 *  @return String the string representation of the doc.
	 * 
	 */
	public String docToString(Document doc)
	{
		OutputStream out = new ByteArrayOutputStream();
	    try 
	    {
	    	 XMLOutputter outputter = new XMLOutputter();
	    	 outputter.output(doc,out );       
	    }
	    catch (IOException e) {
	      System.err.println(e);
	    }
		return out.toString();
	}
	
	/**
	 * This method is a recursive method which allows
	 * to create all the xml node corresponding to the
	 * elemnt in the java representation. It returns the 
	 * Element in the jdom representation.
	 * @param item the IElement to convert.
	 * @return Element the jdom Element corresponding.
	 */
	public Element transformAnIElement(IElement item)
	{
		// Creating the node itself
		Element node = new Element(item.getMeta());
		// Recursive call to create the child's nodes.
		if(item.getName() != null)
			node.setAttribute("name",item.getName());
		if(item.getTitle() != null)
			node.setAttribute("title",item.getTitle());
		node.setText(item.getText());
		Element elchild; 
		if(item.getDesc() != null && item.getDesc() != ""){
			elchild = new Element("desc");
			elchild.setText(item.getDesc());
			node.addContent(elchild);
		}
		
		if(item.getDesc0() != ""){
			elchild = new Element("desc0");
			elchild.setText(item.getDesc0());
			node.addContent(elchild);
		}
		if(item.getDesc1() != ""){
			elchild = new Element("desc1");
			elchild.setText(item.getDesc1());
			node.addContent(elchild);
		}
		if(item.getDesc2() != ""){
			elchild = new Element("desc2");
			elchild.setText(item.getDesc2());
			node.addContent(elchild);
		}
		if(item.getComment() != ""){
			elchild = new Element("comment");
			elchild.setText(item.getComment());
			node.addContent(elchild);
		}
		if(item.getScore() != ""){
			elchild = new Element("score");
			elchild.setText(item.getScore());
			node.addContent(elchild);
		}
		if ( item.getElements() != null )
			for ( IElement child : item.getElements() )
			{
			 node.addContent(transformAnIElement(child));
			}
		return node;
	}
	
	/**
	 * This method put the String lineIN into a file 
	 * created at lineOUT.
	 * 
	 * @param lineIN the String to save.
	 * @param fileOUT the path of the file to create.
	 */
	private void createFile(String lineIN, String fileOUT){
		FileOutputStream fout;		
		try
			{
		    		fout = new FileOutputStream (fileOUT);
		    		new PrintStream(fout).println (lineIN);
		    		fout.close();		
			}
		catch (IOException e){}
        	}
}
