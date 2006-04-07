/* $Id: ISheet.java,v 1.1 2006/04/07 12:04:33 aclerf Exp $
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
package com.ao.o3s.data;


/** <p>A sheet is a class which represents an xml document.
 *  It contains an IElement which is the root of the xml
 *  document in the java modelisation. 
 *  <p> It also contains a property id which is the id in data base 
 *  and a name for clarity since we can find it in the IELements.
 * 
 * @author aclerf
 *
 */	
	
	
public interface ISheet {
	/**
	 * Allow to get the root element.
	 * 
	 * @return root the root element of the sheet.
	 */
	public IElement getRoot();
	/**
	 * Allow to set the root element
	 * @param root the root to set.
	 */
	public void setRoot(IElement root);
	
	/**
	 * Allow to get the id of the sheet.
	 * 
	 * @return id a long which represents the id of the sheet.
	 */
	public long getId();


	/**
	 * Allow to set the id.
	 * @param id the id to set.
	 */
	public void setId(long id);


	/**
	 * Allow to get the name of the sheet.
	 * 
	 * @return name a string which represents the name of the sheet.
	 */
	public String getName();


	/**
	 * Allow to set the name of the sheet.
	 * @param name a string which represents the name to set. 
	 */
	public void setName(String name);
	/**
	 * @param url
	 */

}
