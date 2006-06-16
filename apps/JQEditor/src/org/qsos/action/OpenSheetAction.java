/*
**  $Id: OpenSheetAction.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
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
/**
 * This class represents the Action for open a new sheet
 * 
 * <br>
 * <br>
 * first step:
 * <br>open filedialog to choose the file to open
 * 
 * <br><br>second step:
 * <br>Find the os of the machine
 * 
 * <br><br>Third step:
 * <br>Set the root of the file dialog box
 */
package org.qsos.action;



import org.eclipse.jface.action.Action;
import org.eclipse.jface.resource.ImageDescriptor;
import org.eclipse.swt.SWT;
import org.eclipse.swt.widgets.FileDialog;
import org.eclipse.swt.widgets.Shell;
import org.qsos.data.Messages;
import org.qsos.main.JQ;


/**
 * This class represents the Action for open a new sheet
 * 
 * <br>
 * <br>
 * first step:
 * <br>open filedialog to choose the file to open
 * 
 * <br><br>second step:
 * <br>Find the os of the machine
 * 
 * <br><br>Third step:
 * <br>Set the root of the file dialog box
 */
/**
 * @author MULOT_L
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class OpenSheetAction extends Action
{


	JQ window;

	/**
	 * @param w
	 */
	public OpenSheetAction(JQ w)
	{	
		/**
		 * @param :  window
		 * 
		 */
		window = w;
		setText("Open@Ctrl+O"); //$NON-NLS-1$
		setToolTipText(Messages.getString("OpenSheetAction.toolTipTextOpen")); //$NON-NLS-1$
		setImageDescriptor( ImageDescriptor.createFromFile(null,"images/icons/open_folder_24.ico"));		 //$NON-NLS-1$
	}
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.action.IAction#run()
	 */
	public void run()
	{
		
		FileDialog filedialog;
		Shell shell = new Shell(); 
		filedialog = new FileDialog( shell , SWT.OPEN);  
		filedialog.setFilterExtensions(new String[]{"*.qsos","*.xml","*.*"}); //$NON-NLS-1$ //$NON-NLS-2$ //$NON-NLS-3$
		String adress = filedialog.open();
		
		if ( adress != null)
		{
			String os = System.getProperties().getProperty("os.name"); //$NON-NLS-1$
			
			if(os.startsWith("Windows"))  //$NON-NLS-1$
			{
				adress = "file:///" + adress; //$NON-NLS-1$
			}
			else if(os.startsWith("Linux")) //$NON-NLS-1$
			{
			    adress = "file://" + adress; //$NON-NLS-1$
			}
			else
			{
				//others OS like Mac
				// We must determine the adress mode of URL
			}			
		}		
		if (adress != null)
		{
			window.openSheet(adress);
		}
	
	}	
}
