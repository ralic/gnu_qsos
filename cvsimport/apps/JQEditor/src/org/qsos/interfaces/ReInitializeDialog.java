/*
**  $Id: ReInitializeDialog.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
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

import org.eclipse.jface.dialogs.TitleAreaDialog;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Control;
import org.eclipse.swt.widgets.Label;
import org.eclipse.swt.widgets.Shell;
import org.qsos.data.Messages;



/**
 * This class represents the dialog box to reinitialize a Qsos sheet
 * 
 * 
 * <br>
 * first step:
 * <br>open new dialog box to choose the fields u want to reinitialize 
 * 
 * <br><br>second step:
 * <br>the fields are saved
 * 
 */
/**
 * @author MULOT_L
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class ReInitializeDialog extends TitleAreaDialog
{

	private boolean answer[];
	Button scoreButton;
	Button commentButton;
	Button headerButton;
	

	/**
	 * @param shell
	 */
	public ReInitializeDialog(Shell shell)
	{
		super(shell);
		//, boolean score_, boolean comment_,boolean header_
		answer = new boolean[4];
	

		
	}
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.window.Window#createContents(org.eclipse.swt.widgets.Composite)
	 */
	protected Control createContents(Composite parent)
	{
		Control contents = super.createContents(parent);
		
		// Set the title
		setTitle(Messages.getString("ReInitializeDialog.reInitializeTitle")); //$NON-NLS-1$
		
		// Set the message
		//setMessage("What do you want to reinitialize?");
		
		// Set the image
		// if (image != null) setTitleImage(image);
		
		
		return contents;
		
	}
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.dialogs.Dialog#createDialogArea(org.eclipse.swt.widgets.Composite)
	 */
	protected Control createDialogArea (Composite parent)
	{
		Composite composite = (Composite) super.createDialogArea(parent);
		
		composite.setLayout( new GridLayout(1,true) );
		
		Label explicationLabel = new Label(composite,SWT.CENTER);
		explicationLabel.setText(Messages.getString("ReInitializeDialog.reInitialiseQuestion")); //$NON-NLS-1$
		
		scoreButton = new Button (composite,SWT.CHECK);
		scoreButton.setText(Messages.getString("ReInitializeDialog.score")); //$NON-NLS-1$
		
		commentButton = new Button (composite,SWT.CHECK);
		commentButton.setText(Messages.getString("ReInitializeDialog.comments")); //$NON-NLS-1$
		
		headerButton = new Button (composite,SWT.CHECK);
		headerButton.setText(Messages.getString("ReInitializeDialog.header")); //$NON-NLS-1$
		headerButton.addSelectionListener(new SelectionListener()
		{

			public void widgetSelected(SelectionEvent arg0)
			{
				answer[2] = headerButton.getSelection();
				
			}

			public void widgetDefaultSelected(SelectionEvent arg0)
			{
				// Nothing
				
			}
			
		});
		
		scoreButton.addSelectionListener(new SelectionListener()
				{

					public void widgetSelected(SelectionEvent arg0)
					{
						answer[0]= scoreButton.getSelection();
					}

					public void widgetDefaultSelected(SelectionEvent arg0)
					{
						// Nothing
					}
					
				});
		
		commentButton.addSelectionListener(new SelectionListener()
				{

					public void widgetSelected(SelectionEvent arg0)
					{
						answer[1] = commentButton.getSelection();
					}

					public void widgetDefaultSelected(SelectionEvent arg0)
					{
						// Nothing
						
					}
					
				});
		
		return composite;
		
	}
	
	/**
	 * @return boolean[4]
	 */
	public boolean[] run()
	{
		int ok = this.open();
		if (ok != 0)
		{
			// Score
			answer[0] = false;
			answer[1] = false;
			answer[2] = false;
			
			// User put cancel
			answer[3] = false;
		}
		else
		{
			// User put ok
			answer[3] = true;
		}
		
		return answer;
		
	}

}
