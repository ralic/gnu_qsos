/*
**  $Id: SheetTableTreeLabelProvider.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
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
package org.qsos.interfaces;


import org.eclipse.jface.viewers.ILabelProviderListener;
import org.eclipse.jface.viewers.ITableLabelProvider;
import org.eclipse.swt.SWT;
import org.eclipse.swt.graphics.Image;
import org.eclipse.swt.widgets.Event;
import org.eclipse.swt.widgets.Listener;
import org.qsos.data.IElement;
import org.qsos.data.JQConst;

/**
 * This class provides the labels for PlayerTable
 */
public class SheetTableTreeLabelProvider implements ITableLabelProvider
{
	// Image to display if the player led his team
	private Image image =  null;

	// Constructs a PlayerLabelProvider
	public SheetTableTreeLabelProvider()
	{
		super();

	}

	/**
	 * Gets the image for the specified column
	 * 
	 * @param arg0
	 *            the player
	 * @param arg1
	 *            the column
	 * @return Image
	 */
	public Image getColumnImage(Object arg0, int arg1)
	{
		return image;
	}

	/**
	 * Gets the text for the specified column
	 * 
	 * @param arg0
	 *            the player
	 * @param arg1
	 *            the column
	 * @return String
	 */
	public String getColumnText(Object arg0, int arg1)
	{
		IElement element = (IElement) arg0;
		String text = ""; //$NON-NLS-1$


		if ( element.getElements() == null)
		{
			if ( element.getDesc() == "" ) //$NON-NLS-1$
			{
			
				switch (arg1)
				{
					case JQConst.COLUMN_TITLE:
						text = element.getTitle();
						break;
					case JQConst.COLUMN_DESC:
						if (element.getScore().equalsIgnoreCase(JQConst.SCORE_0))
						{
							//String text = 
							//comboBox.se
							text = element.getDesc0();
							
						}
						else if (element.getScore().equalsIgnoreCase(JQConst.SCORE_1))
						{
							text = element.getDesc1();
						}
						else if (element.getScore().equalsIgnoreCase(JQConst.SCORE_2))
						{
							text = element.getDesc2();
						}
						break;
					case JQConst.COLUMN_COMMENT:
						text = element.getComment();
						break;
					case JQConst.COLUMN_SCORE:
						text = element.getScore();
						break;					
				}

			}
			else 
			{
				switch (arg1)
				{
					case JQConst.COLUMN_TITLE:
						text = element.getDesc();
						break;
					case JQConst.COLUMN_DESC:
						text = element.getComment();
						break;
				}
			}
			
		}
		else
		{
			if ( element.getElements() != null)
			{
				if ( element.getDesc() != "") //$NON-NLS-1$
				{
					switch (arg1)
					{
						case JQConst.COLUMN_TITLE:
							text = element.getDesc();
							break;
					}
				}
				else if ( element.getTitle() != null)
				{
					
					// Case: main section
					switch (arg1)
					{
						case JQConst.COLUMN_TITLE:
							text = element.getTitle();
							break;
					}

				}
				else
				{
					// Last case (for header)
					// Nothing
				}
			}
			else
			{
				// Inexistant Case
				//text = element.toString();
				text = element.getText();
			}
			
		}

		return text;
	}

	/**
	 * Adds a listener
	 * 
	 * @param arg0
	 *            the listener
	 */
	public void addListener(ILabelProviderListener arg0)
	{
		// Throw it away
		new Listener()
		{

			public void handleEvent(Event arg0) {
				switch (arg0.type)
				{
					case SWT.Selection:
						System.out.println(arg0);
						break;
				}
				
			}
			
		};
	}

	/**
	 * Dispose any created resources
	 */
	public void dispose()
	{
		// Dispose
	}

	/**
	 * Returns whether the specified property, if changed, would affect the
	 * label
	 * 
	 * @param arg0
	 *            the player
	 * @param arg1
	 *            the property
	 * @return boolean
	 */
	public boolean isLabelProperty(Object arg0, String arg1)
	{
		return false;
	}

	/**
	 * Removes the specified listener
	 * 
	 * @param arg0
	 *            the listener
	 */
	public void removeListener(ILabelProviderListener arg0)
	{
		// Do nothing
		
	}
}