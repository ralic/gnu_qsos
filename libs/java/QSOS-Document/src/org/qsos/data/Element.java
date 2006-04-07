/* $Id: Element.java,v 1.1 2006/04/07 13:07:48 aclerf Exp $
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
import java.util.LinkedList;
import java.util.List;

/**
 * <p>This class is the reference implementation of the IElement
 * interface. It basicly act as a Java Bean for the model of an
 * O3S sheet line. This model is that each line as four datas :
 * meta, text, name and title. Meta is the xml tag, name and title 
 * are attributes and text is the text inside a tag. This leads to the 
 * point that one Element, the root Element contains all the others 
 * just like in a xml file.</p>
 * 
 * <p>For technical purpore this object has more property. It has 
 * a id property which his id in the datasource( if any)
 * </p>
 * 
 * <p>Aside from this technical property, the JavaBean, has a
 * reference to his container, which is an another IElement which 
 * contains this Element, and a list of sub-IElement which it
 * contains itself.</p>
 * 
 * see O3S Documentation.
 * 
 * @author rpelisse
 * @author aclerf
 *
 */
public class Element implements IElement
{	
    private Long id;
	private String meta;
	private String name;
    private String title;
	private String text;
	
	private String desc;
	private String desc0;
	private String desc1;
	private String desc2;
	private String comment;
	private String score;
	
    
    //This property contains the list of sub-elements    
    private List<IElement> elements = null;
	 //A reference to it 'owner', if any.
	private IElement container;	

	/**********************************
	 ***********CONSTRUCTORS***********
	 *********************************/
	
	/**<p> Allows to initialize the class.
	 */
	public Element(){
		this.id = null;
		this.meta = "";
		this.name = "";
		this.title = "";
		this.text = "";
		this.elements = null;
		this.container = null;

		desc = "";
		desc0 = "";
		desc1 = "";
		desc2 = "";
		comment= "";
		score = "";

	
	}
	
	
	
	/**<p>Allow to construct the value of every attribute of 
	 * the class.</p>
	 * 
	 *
	 * @param id the id to set.
	 * @param meta the meta to set.
	 * @param name the name to set.
	 * @param title the title to set.
	 * @param text the text to set.
	 * @param list the list to set as elements.
	 * @param elem the elem toset as container.
	 */
	public Element(Long id, String meta, String name, String title, String text, List<IElement> list, IElement elem){
		this.id = id;
		this.meta = meta;
		this.name = name;
		this.title = title;
		this.text = text;
		this.elements = list;
		this.container = elem;
		
		
		desc = "";
		desc0 = "";
		desc1 = "";
		desc2 = "";
		comment= "";
		score = "";
	}
	
	
	/**********************************
	 ******GETTERS AND SETTERS*********
	 *********************************/
	
	/**<p>Returns the id of the Element.</p>
	 * 
	 * @return Returns a Long which is the id.
	 */
	public Long getId()
	{
		return id;
	}
	
	/** <p>Allows to set the id of the Element.</p>
	 * 
	 * @param id The id to set.
	 */
	public void setId(Long id)
	{
		this.id = id;
	}	
	
	/** Return the meta in the Element.
	 * 
	 * @return Returns a String which is the meta.
	 */
	public String getMeta()
	{
		return meta;
	}
	
	/** <p>Allows to set the meta of the Element.</p>
	 * 
	 * @param meta the meta to set.
	 */
	public void setMeta(String meta)
	{
		this.meta = meta;
	}

	/** Return the name in the Element.
	 * 
	 * @return Returns a String which is the name.
	 */
	public String getName(){
		return name;
	}

	/** <p>Allows to set the name of the Element.</p>
	 * 
	 * @param name the name to set.
	 */
	public void setName(String name){
		this.name = name;
	}

	/** Return the name in the Element.
	 * 
	 * @return Returns a String which is the name.
	 */
	public String getTitle(){
		return title;
	}
	
	/** <p>Allows to set the title of the Element.</p>
	 * 
	 * @param title the title to set.
	 */
	public void setTitle(String title){
		this.title = title;
	}
	
	/** Return the text in the Element.
	 * 
	 * @return Returns a String which is the text.
	 */
	public String getText()
	{
		return text;
	}
	
	/** <p>Allows to set the text of the Element.</p>
	 * 
	 * @param text the text to set.
	 */
	public void setText(String text)
	{
		this.text = text;
	}
	
	/**<p>Return a List of IElement which represents all the children of the current Element.<p>
	 * @return Returns the list of the sub elements.
	 */
	public List<IElement> getElements()
	{
		return elements;
	}
	/**<p>Allow to set the children of the current Element.</p>
	 * 
	 * @param elements The List of Elements to set.
	 */
	public void setElements(List<IElement> elements)
	{
		this.elements = elements;
	}
	
	/**<p>Return an IElement which represents the parent of the current Element.<p>
	 * 
	 * @return Returns the container.
	 */
	public IElement getContainer()
	{
		return container;
	}/**
     * <p>Allow to set the container (the parent of an Element).</p>
     * @param element the IElement to set as container of the current Element.
     */
	public void setContainer(IElement element)
	{
		this.container = element;
	}
	
	public void setDesc(String desc){
		this.desc = desc;
	}
	
	public String getDesc(){
		return desc;
	}
	
	public void setDesc0(String desc0){
		this.desc0 = desc0;
	}
	

	public String getDesc0(){
		return desc0;
	}
	
	
	public void setDesc1(String desc1){
		this.desc1 = desc1;
	}
	
	public String getDesc1(){
		return desc1;
	}
	
	public void setDesc2(String desc2){
		this.desc2 = desc2;
	}
	
	public String getDesc2(){
		return desc2;
	}
	
	public void setComment(String comment){
		this.comment = comment;
	}
	
	public String getComment(){
		return comment;
	}
	
	public void setScore(String score) throws IOException{
		this.score = score;
	}

	public String getScore(){
		return score;
	}
	
	
	/***************************************
	*************OTHER FUNCTIONS************
	****************************************/
		
	/**
     * <p>Allow to add item to the list of sub element</p>
     * @param item the IElement to add to the children of this object.
     * @return the added element.
     */
	public IElement addElement(IElement item)
	{
		if ( item != null )
		{
			item.setContainer(this);
			if ( this.elements == null )
				this.elements = new LinkedList<IElement>();
			this.elements.add(item);
		}
		return item;
	}
	
	
	/**
     * <p>Allow to delete an item from the list of sub element delete all the children
     * </p>
     * @param item the IElement to delete to the children of this object.
     */
	public void delElement(IElement item)
	{
		if ( item != null )
		{
			if(item.getElements() != null){
			for(IElement child: item.getElements())
				delElement(child);}
			this.elements.remove(item);
		}
	}
	
	
	
	/**
	 * <p>  Returns a string representation of the Element</p>
	 *  @return Returns a string representation of the Element.
	 */
	public String toString()
	{
		String element_as_string = "";
		element_as_string += "text:" + this.getText() + "\t";		
		element_as_string += "meta:" + this.getMeta() + "\t";
		element_as_string += "name:" + this.getName() + "\t";
		
		if ( elements == null || elements.isEmpty() )
			element_as_string += "no childs\n";
		else
			element_as_string += this.elements.size() + " childs\n";
		return element_as_string;
	}

	/**
	 * <p>Return a string version of the Element and its childs.
	 * This method is designed to ease unit test and debug and
	 * <i> is not </i> to be used for anything else.</p>
	 * @return string version of the Element.
	 */
	public String tree()
	{
		return treeToString(this,"");
	}
	private static String treeToString(IElement element,String indent)
	{
		String tree = "";
		tree += indent + element.toString();
		List<IElement> listElements = element.getElements();
		if ( listElements != null && ! listElements.isEmpty() )
			for ( IElement containedElement : element.getElements() )
				tree += indent + Element.treeToString(containedElement,indent + "\t");
		return tree;
	}
}
