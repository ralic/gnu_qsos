/* $Id: IElement.java,v 1.1 2006/04/07 13:07:48 aclerf Exp $
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
package org.qsos.data;

import java.io.IOException;
import java.util.List;


/**
 * This interface should be implemented by an Element
 * <ul>
 * <li>Meta : name of the tag ( ex : "Element" ).
 * <li>Name : the name is an attribute ( ex : "historicissue"). 
 * <li>Title : the title is an attribute ( ex : "Historic Issue").
 * <li>Text : the text inside the tag ( ex : "Did the project meet
 *  important issue in the past years?").
 * </ul>
 * 
 * @author rpelisse
 * @author aclerf
 * 
 */


public interface IElement 
{
	/**********************************
	 ******GETTERS AND SETTERS*********
	 *********************************/
	
	/**<p>Returns the id of the Element.</p>
	 * 
	 * @return Returns a Long which is the id.
	 */
	public Long getId();
	
	/** <p>Allows to set the id of the Element.</p>
	 * 
	 * @param id The id to set.
	 */
	public void setId(Long id);
	
	/** Return the meta in the Element.
	 * 
	 * @return Returns a String which is the meta.
	 */
	public String getMeta();
	
	/** <p>Allows to set the meta of the Element.</p>
	 * 
	 * @param meta the meta to set.
	 */
	public void setMeta(String meta);

	/** Return the name in the Element.
	 * 
	 * @return Returns a String which is the name.
	 */
	public String getName();

	/** <p>Allows to set the name of the Element.</p>
	 * 
	 * @param name the name to set.
	 */
	public void setName(String name);

	/** Return the name in the Element.
	 * 
	 * @return Returns a String which is the name.
	 */
	public String getTitle();
	
	/** <p>Allows to set the title of the Element.</p>
	 * 
	 * @param title the title to set.
	 */
	public void setTitle(String title);
	
	/** Return the text in the Element.
	 * 
	 * @return Returns a String which is the text.
	 */
	public String getText();

	/** <p>Allows to set the text of the Element.</p>
	 * 
	 * @param text the text to set.
	 */
	public void setText(String text);
	
	/**<p>Return a List of IElement which represents all the children of the current Element.<p>
	 * @return Returns the list of the sub elements.
	 */
	public List<IElement> getElements();

	/**<p>Allow to set the children of the current Element.</p>
	 * 
	 * @param elements The List of Elements to set.
	 */
	public void setElements(List<IElement> elements);
	
	/**<p>Return an IElement which represents the parent of the current Element.<p>
	 * 
	 * @return IElement the container.
	 */
	public IElement getContainer();
	
	/**
     * <p>Allow to set the container (the parent of an Element).</p>
     * @param element the IElement to set as container of the current Element.
     */
	public void setContainer(IElement element);

	/***************************************
	*************OTHER FUNCTIONS************
	****************************************/
	
	
	/**
     * <p>Allow to add item to the list of sub element</p>
     * @param item the IElement to add to the children of this object.
     * @return the added element.
     */
	public IElement addElement(IElement item);
	
	/**
	 * <p>  Returns a string representation of the Element</p>
	 *  @return Returns a string representation of the Element.
	 */
	public String toString();

	/**
	 * <p>Return a string version of the Element and its childs.
	 * This method is designed to ease unit test and debug and
	 * <i> is not </i> to be used for anything else.</p>
	 * @return string version of the Element.
	 */
	public String tree();

	/**
	 * 
	 */
	public void setDesc(String desc);

	
	public String getDesc();
	/**
	 * @param text
	 */
	public void setDesc0(String text);

	/**
	 * @param text
	 */
	
	public String getDesc0();
	public void setDesc1(String text);
	public String getDesc1();

	/**
	 * @param text
	 */
	public void setDesc2(String text);
	public String getDesc2();

	/**
	 * @param text
	 */
	public void setComment(String text);
	public String getComment();

	/**
	 * @param text
	 * @throws IOException 
	 */
	public void setScore(String text) throws IOException;
	public String getScore();

	/**
	 * @param author
	 */
	public void delElement(IElement author);

	/**
	 * @param numDesc
	 * @return
	 */

}


