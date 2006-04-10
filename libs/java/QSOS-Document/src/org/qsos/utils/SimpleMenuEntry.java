/* $Id: SimpleMenuEntry.java,v 1.1 2006/04/10 16:10:00 aclerf Exp $
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

/**
 * 
 */

package org.qsos.utils;

/**
 * @author aclerf
 *
 */
public class SimpleMenuEntry {
	private int deep;
	private String name;
	private String title;
	
	
	public SimpleMenuEntry(int deep, String name, String title){
		this.deep = deep;
		this.name = name;
		this.title = title;
	}
	
	
	public int getDeep(){
		return this.deep;
	}
	public void setDeep(int deep){
		this.deep = deep;
	}	
	public String getName(){
		return this.name;
	}
	public void setName(String name){
		this.name = name;
	}

	public String getTitle(){
		return this.title;
	}
	public void setTitle(String title){
		this.title = title;
	}
	
	
}
