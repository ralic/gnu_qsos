/* $Id: DesXMLizer.java,v 1.1 2006/04/07 13:09:08 aclerf Exp $
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
package org.qsos.transformation;

import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.input.SAXBuilder;
import org.qsos.data.IElement;
import org.qsos.data.ISheet;
import org.qsos.data.Sheet;

import java.io.IOException;
import java.net.URL;
import java.util.List;
//import com.ao.o3s.utils.ILibQSOS;
//import com.ao.o3s.utils.LibQSOS;

/**<p>
 * The aim of this class is to do the conversion between a xml
 * document and the java modelisation. In order to do the conversion,
 * the library jdom is used.
 * <p> This class implements IDesXMLizer.
 * 
 * @author aclerf
 *
 */

public class DesXMLizer implements IDesXMLizer{
	
	/**
	 * <p> This method transforms an xml file given by is url
	 * (use "file:///path/toto.xml" if local) and returns an 
	 * IElement which is the root element of the xml file 
	 * in the java modelisation.  
	 * @exception JDOMException.
	 * @param url the url of the xml file.
	 * @return ISheet the ISheet in the java modelisation.
	 */
	public ISheet transformFromXML(URL url) throws JDOMException, IOException{
		SAXBuilder sax = new SAXBuilder();
		try 	{
		Document docJDom = sax.build(url);
		Element document = docJDom.getRootElement();
		ISheet sheet = new Sheet(transformAnElement(document));
//		ILibQSOS tools = new LibQSOS(sheet);
	//	String name = tools.getElementsByMeta(sheet.getRoot(),"appname").get(0).getText();
		//sheet.setName(name);
		return sheet;
		}
		catch(JDOMException e) {}
		return null;
	}
	
	/**<p> This method is private due to the fact that it 
	 * is a recursive auxilary method of <code>transformFromXML
	 * </code>
	 * 
	 * @param target the IElement root of the branch.
	 * @return item a root of another branch.
	 * 
	 */
	@SuppressWarnings("unchecked")
	public IElement transformAnElement(Element target){
		IElement item = new org.qsos.data.Element();
		IElement child;
		List<Element> children = target.getChildren();
		item.setMeta(target.getName());
		if(target.getText()!=null)
		{
			/* Work on the format not to transform the xml */
			if (target.getText().startsWith("\n"))
				item.setText("\n");
			else if (target.getText() == "")
				item.setText("\n");
			else item.setText(target.getText());
		}
		else item.setText("\n");
		item.setTitle(target.getAttributeValue("title"));
		item.setName(target.getAttributeValue("name"));
		if ( children != null )
			for (Element elChild : children)
				{
				if(elChild.getName().equals("desc"))	{
					item.setDesc(elChild.getText());
					}
				else if(elChild.getName().equals("desc0")){
					item.setDesc0(elChild.getText());
					}					
				else if(elChild.getName().equals("desc1")){
					item.setDesc1(elChild.getText());
					}					
				else if(elChild.getName().equals("desc2")){
					item.setDesc2(elChild.getText());
				}					
				else if(elChild.getName().equals("comment")){
					item.setComment(elChild.getText());
				}					
				else if(elChild.getName().equals("score")){
					if(elChild.getText() != "")
					{
						try {
							item.setScore(elChild.getText());
						}
						catch(IOException e){
							System.err.println("Invalid value score must be between 0 and 2");
						}
					}
				}					
				
				else {
					child = transformAnElement(elChild);
					child.setContainer(item);
					item.addElement(child);
					}
				}
		return item;
	}
}