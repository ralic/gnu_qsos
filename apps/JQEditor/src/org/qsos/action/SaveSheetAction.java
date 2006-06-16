/*
**  $Id: SaveSheetAction.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
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
package org.qsos.action;





import org.eclipse.jface.action.Action;
import org.eclipse.jface.dialogs.MessageDialog;
import org.eclipse.jface.resource.ImageDescriptor;
import org.eclipse.swt.SWT;
import org.eclipse.swt.widgets.FileDialog;
import org.qsos.data.Messages;
import org.qsos.main.JQ;

/**
 * This class represents the Action to save a Qsos sheet
 * 
 * 
 * <br>
 * first step:
 * <br>open new dialog box to choose the directory and the name of the save file
 * 
 * <br><br>second step:
 * <br>the user confirm the directory and the name of the sheet
 * 
 *  * <br><br>third step:
 * <br>the sheet is saved in the new directory
 * 
 */
/**
 * @author MULOT_L
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class SaveSheetAction extends Action
{
	JQ window;
	
	/**
	 * @param w
	 */
	public SaveSheetAction(JQ w)
	{
		super("&Save@Ctrl+s",ImageDescriptor.createFromFile(SaveSheetAction.class,"/images/icons/save.png")); //$NON-NLS-1$ //$NON-NLS-2$
		
		setImageDescriptor( ImageDescriptor.createFromFile(null,"images/icons/save.png")); //$NON-NLS-1$
		//setDisabledImageDescriptor(ImageDescriptor.createFromFile(SaveSheetAction.class,"images/icons/save.png"));

		setToolTipText(Messages.getString("SaveSheetAction.toolTipTextSave")); //$NON-NLS-1$

		window = w;
		

	}
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.action.IAction#run()
	 */
	public void run()
	{
		
		if (window.getCTabFolder().getItemCount() > 0)
		{
			final FileDialog saveDialog = new FileDialog ( window.getShell() , SWT.SAVE);  
			saveDialog.setFilterExtensions(new String[]{"*.qsos"}); //$NON-NLS-1$
			String adressSaveFile = saveDialog.open();
			
			
			if ( adressSaveFile != null )
			{
				window.saveSheet(adressSaveFile);
			}
		}
		else
		{
			MessageDialog.openWarning(window.getShell(),Messages.getString("SaveSheetAction.error"),Messages.getString("SaveSheetAction.errorMessageNotOpenDoc"));  //$NON-NLS-1$ //$NON-NLS-2$
		}
		
	}
}

