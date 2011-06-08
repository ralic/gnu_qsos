/* $Id: Search.java,v 1.4 2006/04/13 12:57:37 aclerf Exp $
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
package org.qsos.utils;

import java.io.IOException;
import java.util.LinkedList;
import java.util.List;

import org.qsos.data.IElement;
import org.qsos.data.ISheet;


/**
 * @author Arthur Clerfeuille <arthur.clerfeuille@atosorigin.com>
 *
 */
public class Search {
	private ISheet sheet;
	private String name;
	private int numDesc;
	private String string2set;
	private String type;
	
	public void setSheet(ISheet sheet){
		this.sheet = sheet;
	}
	
	public List<IElement> searchByName() throws IOException{
		List<IElement> list = new LinkedList<IElement>();		
		return searchRecByName(sheet.getRoot(),list);
	}
	
	
	public List<IElement> searchRecByName(IElement target,List<IElement> list) throws IOException{
		if (target.getElements() != null)
			{
				for (IElement child : target.getElements())
					{
						if(child.getName() != null && child.getName().equals(name)){
							list.add(child);
						}
						searchRecByName(child,list);
					}
				return list;
			}
	return null;
	}

	public List<IElement> searchByMeta() throws IOException{
		List<IElement> list = new LinkedList<IElement>();		
		return searchRecByMeta(sheet.getRoot(),list);
	}
	
	
	public List<IElement> searchRecByMeta(IElement target,List<IElement> list){
		if (target.getElements() != null)
			{
				for (IElement child : target.getElements())
					{
						if(child.getMeta() != null && child.getMeta().equals(name)){
							list.add(child);
						}
						searchRecByMeta(child,list);
					}
				return list;
			}
		return null;
	}

	
	
	
	/**
	 * @param name
	 */
	public void setName(String name) {
		this.name = name;
	}

	/**
	 * @param numDesc
	 */
	public void setNumDesc(int numDesc) {
		this.numDesc = numDesc;
	}

	/**
	 * @return
	 * @throws IOException 
	 */
	public String searchString() throws IOException {
		List<IElement> list = new LinkedList<IElement>();
		if( type.equals("name") )
			list = searchByName();
		else if (type.equals("meta"))
			list = searchByMeta();
		else throw new IOException();
		if (list.size() > 1)
		{
			String res = "";
			for (IElement member : list)
				res = res + member.getText() + "\t";
			return res;
		}
		else if(list.size() == 1) 
			{
				IElement temp = list.get(0);
				if(numDesc ==-1)
					return temp.getText();
				else if(numDesc == 0)
					return temp.getDesc0();
				else if (numDesc == 1)
					return temp.getDesc1();
				else if (numDesc == 2)
					return temp.getDesc2();
				else throw new IOException();
			}
		else throw new IOException();
	}

	public IElement searchIElement() throws IOException{
		List<IElement> list = new LinkedList<IElement>();
		if( type.equals("name") )
			list = searchByName();
		else if (type.equals("meta"))
			list = searchByMeta();
		if (list.size() == 1)
		{
			return list.get(0);
		}
		else throw new IOException();
	}
	
	public void searchAndSet(){
		try {
			searchIElement().setText(string2set);	
		} catch (IOException e) {
			System.err.println("Can't find Key");
		}
	}
		
	public String searchAndGetString(){
		try {
			return searchString();		
		} catch (IOException e) {
			System.err.println("Can't find Key");
		}
		return null;
	}
	
	public IElement searchAndGetIElement(){
		try {
			return searchIElement();		
		} catch (IOException e) {
			System.err.println("Can't find Key");
		}
		return null;
	}

	/**
	 * @param string2set
	 */
	public void setString2set(String string2set) {
		this.string2set = string2set;
		
	}

	/**
	 * @param type
	 */
	public void setType(String type) {
		this.type = type;
		
	}

	/**
	 * @return
	 */
	public ISheet getSheet() {
		return this.sheet;
	}

	 public List<SimpleMenuEntry> getSimpleTree(){
		 List<SimpleMenuEntry> list = new LinkedList<SimpleMenuEntry>();
		 IElement root = sheet.getRoot();
		 int deep = 0;
		 SimpleMenuEntry menu = new SimpleMenuEntry(0,root.getName(),root.getTitle());
		 list.add(menu);
		 getSimpleTreeRec(root,deep,list);
		 return list;
	 }
	 
	 
	 /**
	 * @param root
	 * @param deep
	 * @param list
	 * @return
	 */
	private void getSimpleTreeRec(IElement root, int deep, List<SimpleMenuEntry> list){
		if(root.getElements() != null)
		{
			for(IElement child : root.getElements())
			{
				SimpleMenuEntry menu = new SimpleMenuEntry(deep + 1,child.getName(),child.getTitle());
				list.add(menu);
				getSimpleTreeRec(child,deep+1,list);
			}		
		}
	}
	
	
	
}
