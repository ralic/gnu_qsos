/* $Id: LibQSOS.java,v 1.4 2006/04/13 12:57:37 aclerf Exp $
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
import java.net.URL;
import java.util.LinkedList;
import java.util.List;

import org.jdom.JDOMException;
import org.qsos.data.Element;
import org.qsos.data.IElement;
import org.qsos.data.ISheet;
import org.qsos.transformation.DesXMLizer;
import org.qsos.transformation.IDesXMLizer;
import org.qsos.transformation.IXMLizer;
import org.qsos.transformation.XMLizer;


/** This class implements the methodes to be used in 
 * a QSOS Editor in Java. It uses a search class so if the xml
 * format is modified, a very few modifications would have to be done. 
 * It implements the interface ILibQSOS.
 * @author Arthur Clerfeuille <arthur.clerfeuille@atosorigin.com>
 *
 */
public class LibQSOS implements ILibQSOS{
	private Search search;
	
	
	/**Allows to initialize the class search with the right parameters
	 * @param name the name to search
	 * @param numDesc int representing the number of the description to search
	 */
	private void reInitSearch(String type,String name, int numDesc) {
		search.setType(type);
		search.setName(name);
		search.setNumDesc(numDesc);
	}
	
	
	
	/**Allows to initialize the search class and set the parameter
	 * 
	 * @param type the type of search
	 * @param name the name to search
	 * @param numDesc int representing the number of the description to search
	 * @param string2set the string to set
	 */
	private void reInitSearch(String type, String name, int numDesc, String string2set) {
		search.setType(type);
		search.setName(name);
		search.setNumDesc(numDesc);
		search.setString2set(string2set);
	}
	
	
	/**
	 * Constructor for the class LibQSOS.
	 *
	 */
	 public LibQSOS(){
		search = new Search();
		reInitSearch("","",-1);
	}

	/**
	 * Constructor for the class LibQSOS which sets the sheet.
	 *
	 */
	 public LibQSOS(ISheet sheet){
		search = new Search();
		reInitSearch("","",-1);
		search.setSheet(sheet);
	}
	
	/**
	 * Allows to load the java model corresponding to the xml document found at the 
	 * url.
	 * @param url the URL where the xml document is get.
	 */
	 public void load(URL url){
		IDesXMLizer desxml = new DesXMLizer();
		try {
			search.setSheet(desxml.transformFromXML(url));
		} 
		catch (JDOMException e) {
			System.err.println("Can't load file");
		} 
		catch (IOException e) {
			System.err.println("Can't find file");
		}
	}
	 public ISheet getSheet(){
		 return search.getSheet();
	 }
	 
	 public void setSheet(ISheet sheet){
		 search.setSheet(sheet);
	 }
	 
	 public List<SimpleMenuEntry> getSimpleTree(){
		 return search.getSimpleTree();
	 }
	 
	/**
	 * Fonction de debuggage
	 */
	 
	public String Debugaffichage(List<SimpleMenuEntry> list){
		String res = "";
		
		for(SimpleMenuEntry menu : list)
		{	if(menu.getName()!=null)
			{
				for(int i=0; i<=menu.getDeep();i++)
				{
					res = res + " ";
				}
				res = res + menu.getName() + "\n";
			}
		}
		return res;
	}
	
	
	 
	/**
	 * Allows to get the description number numDesc of the element called name.
	 * @param name the name to search.
	 * @param numDesc int representing the number of the description to search.
	 * @return a String corresponding to the description asked.
	 */
	 public String getDescByName(String name, int numDesc){
		try {
			reInitSearch("name",name,numDesc);
			return search.searchString();
		} catch (IOException e) {
			System.err.println("Can't find Key");
		}
		return null;
	}
	
	

	/**
	 * Allows to set a comment to an element given by his name.
	 * 
	 * @param name the name of the element.
	 * @param comment the comment to set.
	 */
	 public void setCommentByName(String name,String comment){
		try {
			reInitSearch("name",name,-1);
			search.searchIElement().setComment(comment);
		} catch (IOException e) {
			System.err.println("Can't find Key");
		}
	}
	
	/**
	 * Allows to get the comment on an element.
	 * 
	 * @param name the name of the element to get.
	 * @return a String corresponding to the comment of the element asked.
	 */
	 public String getCommentByName(String name){
		try {
			reInitSearch("name", name,-1);
			return search.searchIElement().getComment();
		} catch (IOException e) {
			System.err.println("Can't find Key");
		}
		return null;
	}
	
	/** Allows to get the score of an element.
	 * 
	 * @param name the name of the element.
	 * @return a String representing the score of the Element.
	 */
	public String getScoreByName(String name){
		try {
			reInitSearch("name",name,-1);
			return search.searchIElement().getScore();		
			} catch (IOException e) {
			System.err.println("Can't find Key");
		}
		return null;
	}
	
	/**Allows to set the score of an element
	 * 
	 * @param name the name of the element.
	 * @param score a String representing the score to set.
	 */
	public void setScoreByName(String name,String score){
		try {
			reInitSearch("name",name,-1);
			search.searchIElement().setScore(score);		
			} catch (IOException e) {
			System.err.println("Can't find Key");
		}
	}	
	
	/**
	 * Allows to get the name of all the authors.
	 * 
	 * @return a String that contains the names of all the authors.
	 */
	public String getAuthors(){
		try {
			reInitSearch("meta", "name",-1);
			return search.searchString();		
		} catch (IOException e) {
			System.err.println("Can't find Key");
		}
		return null;
	}
	
	/**Allows to add an author to the list of authors.
	 * 
	 * @param nameString the name of the author to add.
	 * @param emailString the email of the author to add.
	 */
	public void addAuthor(String nameString, String emailString) {
		try {
			reInitSearch("meta", "authors",-1);
			IElement temp = search.searchIElement();
			IElement author = new Element(null, "author", null, null, "", null, temp);
			IElement email;
			IElement name;
			if(emailString !=null)
				email = new Element(null, "email", null, null, emailString, null, author);
			else email = new Element(null, "email", null, null, null, null, author);
			if(nameString !=null)
				name = new Element(null, "name", null, null, nameString, null, author);
			else name = new Element(null, "name", "", "", "", null, author);
			author.addElement(email);
			author.addElement(name);
			temp.addElement(author);
		} catch (IOException e) {
			System.err.println("Can't find Key");
		}
	}

	/**Allows to delete an author.
	 * 
	 * @param name the name of the author to delete.
	 */
	public void delAuthor(String name){
		try {
			reInitSearch("meta", "authors",-1);
			IElement authors = search.searchIElement();
			List<IElement> list = new LinkedList<IElement>();
			for(IElement author : authors.getElements())
			{
				for(IElement child: author.getElements())
				{
					if(child.getMeta().equals("name") && child.getText().contains(name))
					{
						list.add(author);
					}
				}
			}
			for(IElement aut:list)
			{
				authors.delElement(aut);
			}
		} catch (IOException e) {
			System.err.println("Can't find Key");
		}
	}

	/* All the method describes backwards allow to get or set the element inside the header */
	
	
	/**Allows to get the application name.
	 * 
	 * @return a string corresponding to the application name.
	 */
	public String getAppname(){
		reInitSearch("meta", "appname",-1);
		return search.searchAndGetString();	
		
	}
	
	
	/**Allows to set the application name.
	 * 
	 * 
	 * @param appname the application name to set.
	 */
	public void setAppname(String appname){
		reInitSearch("meta", "appname",-1,appname);
		search.searchAndSet();	
		
	}
	
	
	/**Allows to get the language.
	 * 
	 * @return a string corresponding to the language.
	 */
	public String getLanguage(){
		reInitSearch("meta", "language",-1);
		return search.searchAndGetString();	
	}
	
	
	/**Allows to set the language.
	 * 
	 * 
	 * @param language the language to set.
	 */

	public void setLanguage(String language){
		reInitSearch("meta", "language",-1,language);
		search.searchAndSet();	
	}

	/**Allows to get the release number.
	 * 
	 * @return a string corresponding to the release number.
	 */
	public String getRelease(){
		reInitSearch("meta", "release",-1);
		return search.searchAndGetString();
	}
	
	
	/**Allows to set the release.
	 * 
	 * 
	 * @param release the release to set.
	 */

	public void setRelease(String release){
		reInitSearch("meta", "release",-1,release);
		search.searchAndSet();	
	}
	
	
	/** Not implemented yet 
	 */
	public String getLicenselist(){
		return null;
	}

	
	/**Allows to get the license Id.
	 * 
	 * @return a string corresponding to the License Id.
	 */
	public String getLicenseId(){
		reInitSearch("meta", "licenseid",-1);
		return search.searchAndGetString();
	}
	
	
	/**Allows to set the license Id.
	 * 
	 * 
	 * @param licenseId the license Id to set.
	 */
	public void setLicenseId(String licenseId){
		reInitSearch("meta", "licenseid",-1,licenseId);
		search.searchAndSet();	
	}
	
	
	/**Allows to get the license Description.
	 * 
	 * @return a string corresponding to the license Description.
	 */	
	public String getLicenseDesc(){
		reInitSearch("meta", "licensedesc",-1);
		return search.searchAndGetString();
	}
	
	
	/**Allows to set the license Description.
	 * 
	 * 
	 * @param licensedesc the license Description to set.
	 */

	public void setLicenseDesc(String licensedesc){
		reInitSearch("meta", "licensedesc",-1,licensedesc);
		search.searchAndSet();	
	}
	
	
	/**Allows to get the url.
	 * 
	 * @return a string corresponding to the url.
	 */
	public String getUrl(){
		reInitSearch("meta", "url",-1);
		return search.searchAndGetString();
	}
	
	
	/**Allows to set the url.
	 * 
	 * 
	 * @param url the url to set.
	 */

	public void setUrl(String url){
		reInitSearch("meta", "url",-1,url);
		search.searchAndSet();	
	}
	
	
	/**Allows to get the description.
	 * 
	 * @return a string corresponding to the description.
	 */
	public String getDesc(){
		reInitSearch("meta", "header",-1);
		return search.searchAndGetIElement().getDesc();
	}
	
	
	/**Allows to set the description.
	 * 
	 * 
	 * @param desc the description to set.
	 */
	public void setDesc(String desc){
		reInitSearch("meta", "header",-1);
		search.searchAndGetIElement().setDesc(desc);
	}
	
	
	/**Allows to get the demonstration url.
	 * 
	 * @return a string corresponding to the demonstration url.
	 */
	public String getDemoUrl(){
		reInitSearch("meta", "demourl",-1);
		return search.searchAndGetString();
	}
	
	
	/**Allows to set the demonstration url.
	 * 
	 * 
	 * @param demourl the demonstration url to set.
	 */
	public void setDemoUrl(String demourl){
		reInitSearch("meta", "demourl",-1,demourl);
		search.searchAndSet();	
	}
	
	
	/**Allows to get the QSOS format.
	 * 
	 * @return a string corresponding to the QSOS format.
	 */
	public String getQsosformat(){
		reInitSearch("meta", "qsosformat",-1);
		return search.searchAndGetString();
	}
	
	
	/**Allows to set the QSOS format.
	 * 
	 * 
	 * @param qsosformat the QSOS format to set.
	 */
	public void setQsosformat(String qsosformat){
		reInitSearch("meta", "qsosformat",-1,qsosformat);
		search.searchAndSet();	
	}
	
	
	/**Allows to get the QSOS specific format.
	 * 
	 * @return a string corresponding to the QSOS specific format.
	 */
	public String getQsosspecificformat(){
		reInitSearch("meta", "qsosspecificformat",-1);
		return search.searchAndGetString();
	}
	
	
	/**Allows to set the QSOS specific format.
	 * 
	 * 
	 * @param qsosspecificformat the QSOS specific format to set.
	 */

	public void setQsosspecificformat(String qsosspecificformat){
		reInitSearch("meta", "qsosspecificformat",-1,qsosspecificformat);
		search.searchAndSet();	
	}
	
	
	/**Allows to get the application family in QSOS.
	 * 
	 * @return a string corresponding to the application family in QSOS.
	 */
	public String getQsosappfamily(){
		reInitSearch("meta", "qsosappfamily",-1);
		return search.searchAndGetString();
	}
	
	
	/**Allows to set the application family in QSOS.
	 * 
	 * 
	 * @param qsosappfamily the application family in QSOS to set.
	 */

	public void setQsosappfamily(String qsosappfamily){
		reInitSearch("meta", "qsosappfamily",-1,qsosappfamily);
		search.searchAndSet();	
	}

	/**Allows to write the xml file at the given path. 
	 * This method has a problem since it degrated the xml file (
	 * not the datas but the presentation).
	 * It will be fixed in the next version
	 * 
	 * @param path
	 */
	public void write(String path) {
		IXMLizer xml = new XMLizer();
		xml.transformToXml(search.getSheet(),path);
	}
}
