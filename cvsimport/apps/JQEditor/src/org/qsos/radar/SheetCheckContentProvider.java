/*
**  $Id: SheetCheckContentProvider.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
**
**  Copyright (C) 2006 ESME SUDRIA ( www.esme.fr ) 
**
**  Authors: 
**	BOUCHER Nicolas <shoub_n@hotmail.com>
**  	MODELIN Maxence  <maxence_modelin@hotmail.com>
**  	MULOT Louis <vindic@noos.fr>
**
**  This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
**
**
*/
package org.qsos.radar;

import org.eclipse.jface.viewers.ITreeContentProvider;
import org.eclipse.jface.viewers.Viewer;
import org.eclipse.swt.custom.CTabFolder;
import org.qsos.data.Element;
import org.qsos.data.ISheet;
import org.qsos.interfaces.SheetCTabItem;

/**
 * This class provides content for the checkboxtree
 * 
 */
public class SheetCheckContentProvider implements ITreeContentProvider
{

private static final Object[] EMPTY = new Object[] {};
	
	private ISheet iSheet;
	
	/**
	 * Constructor that also initalizes the Sheet
	 * 
	 * @param cTabFolder
	 */
	public SheetCheckContentProvider(CTabFolder cTabFolder)
	{
		super();
		iSheet = ((SheetCTabItem) cTabFolder.getSelection()).getLibQSOS().getSheet();
	}

	/**
	 * 
	 * @param arg0
	 * 			Object
	 * @return Object[]
	 */
	public Object[] getChildren(Object arg0) 
	{
		if (arg0 instanceof Element) 
		{
			if ( ((Element) arg0).getElements()!= null )
			{
				return ((Element) arg0).getElements().toArray();
			}
			else
			{
				return EMPTY;
			}
		}
		
		// If error with sheet
		return EMPTY;
	}
	
	/**
	 * Gets the parent Element for an other Element
	 * 
	 * @param arg0 the Element
	 * @return Object
	 */
	public Object getParent(Object arg0) 
	{
		return ((Element) arg0).getContainer();
	}
	
	/**
	 * Gets whether this Element has children
	 * 
	 * @param arg0 the Element
	 * @return boolean
	 */
	public boolean hasChildren(Object arg0) 
	{
		return getChildren(arg0).length > 0;
	}
	
	/**
	 * Gets the elements for the table
	 * 
	 * @param arg0 the model
	 * @return Object[]
	 */
	public Object[] getElements(Object arg0) 
	{

		return ((Element) iSheet.getRoot()).getElements().toArray();
	}
	
	/**
	 * Disposes any resources
	 */
	public void dispose() 
	{
		// We don't create any resources, so we don't dispose any
	}
	
	/**
	 * Called when the input changes
	 * 
	 * @param arg0 the parent viewer
	 * @param arg1 the old input
	 * @param arg2 the new input
	 */
	public void inputChanged(Viewer arg0, Object arg1, Object arg2) 
	{
		// Nothing to do
	}
}
