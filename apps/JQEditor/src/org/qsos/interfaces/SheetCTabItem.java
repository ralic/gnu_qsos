/*
**  $Id: SheetCTabItem.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
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


import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import org.eclipse.jface.dialogs.MessageDialog;
import org.eclipse.jface.viewers.ISelection;
import org.eclipse.jface.viewers.StructuredSelection;
import org.eclipse.jface.viewers.TableTreeViewer;
import org.eclipse.swt.SWT;
import org.eclipse.swt.custom.CTabFolder;
import org.eclipse.swt.custom.CTabItem;
import org.eclipse.swt.custom.TableTreeItem;
import org.eclipse.swt.events.KeyEvent;
import org.eclipse.swt.events.KeyListener;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.events.VerifyEvent;
import org.eclipse.swt.events.VerifyListener;
import org.eclipse.swt.graphics.Image;
import org.eclipse.swt.layout.FormAttachment;
import org.eclipse.swt.layout.FormData;
import org.eclipse.swt.layout.FormLayout;
import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Group;
import org.eclipse.swt.widgets.Label;
import org.eclipse.swt.widgets.Text;
import org.qsos.data.IElement;
import org.qsos.data.ImageRegistryQSOS;
import org.qsos.data.JQConst;
import org.qsos.main.JQ;
import org.qsos.utils.LibQSOS;
import org.qsos.data.Messages;


/**
 * @author MODELIN_M
 *
 * SheetCTabItem is a class extends of CTabItem.
 * This class has methods specialy implemented for the JQ application.
 *
 * 
 * 
 */
public class SheetCTabItem extends CTabItem
{
	
	private JQ jqApp;
	private LibQSOS libQSOS;
	private LibQSOS saveLibQSOS;
	private CTabFolder cTF;
	private TableTreeViewer tableTreeViewer;
	
	private Group headerGroup;
	private Group answerGroup;
	private Group activeGroup;
	private Group nonActiveGroup;

	// HeaderGroup's text
	private Text textLanguage;
	private Text textAppname;
	private Text textRelease;
	private Text textLicense;
	private Text textDesc;
	private Text textUrl;
	private Text textAuthors;
	private Text textAuthorsMail;
	
	// AnswerGroup'Button and text
	private Button buttonDesc0;
	private Button buttonDesc1;
	private Button buttonDesc2;
	private Text textComment;
	private Text textScore;
	
	// Active element
	private IElement element;
	
	// URL of the sheet
	private URL urlSheet;


	
	/**
	 * Constructor
	 * 
	 * @param arg0
	 * @param arg1
	 * @param adress_sheet
	 * @param answerMainCompo
 	 * @param w
	 */
	public SheetCTabItem (CTabFolder arg0, int arg1, String adress_sheet, Composite answerMainCompo, JQ w)
	{
		super(arg0, arg1);
		
		cTF = arg0;
		jqApp = w;
		
		
		// Initialize adress of the sheet 
		InitializeLibQSOS (adress_sheet);	
		
		// Create the answerComposite			
		createHeaderGroup(answerMainCompo);
		createAnswerGroup(answerMainCompo);
		
		// Rename the tabulation
		if (saveLibQSOS.getAppname() != null)
		{
			if  (saveLibQSOS.getRelease() != null)
			{
				this.setText ( saveLibQSOS.getAppname() + " " + saveLibQSOS.getRelease() ); //$NON-NLS-1$
			}
			else
			{
				this.setText ( saveLibQSOS.getAppname() );
			}
		}
		else
		{
			// Cast a int to string
			this.setText ( "" + cTF.getItemCount() ); //$NON-NLS-1$
		}
		

	}
	
	/**
	 * 
	 * 
	 * @param adress_sheet
	 */
	private void InitializeLibQSOS(String adress_sheet)
	{
		
		
		// Creation of a LibQSOS Document
		libQSOS = new LibQSOS();
		
		// Creation of a save of LibQSOS:
		saveLibQSOS = new LibQSOS();
		
		try
		{	
			// Change adress String to URL adress
			urlSheet = new URL (adress_sheet);
			
			// Document loading
			libQSOS.load(urlSheet);
			saveLibQSOS.load(urlSheet);

		} catch (MalformedURLException e)
		{
			MessageDialog.openError(getParent().getShell(),Messages.getString("SheetCTabItem.error"),Messages.getString("SheetCTabItem.errorLoading"));  //$NON-NLS-1$ //$NON-NLS-2$
			e.printStackTrace();
				
		}
	}
	
	
	
	
	/**
	 * Method for create the headerGroup.
	 * The composite is create and return for show properties
	 * 
	 * @param headerComposite
	 */
	protected void createHeaderGroup(Composite parent)
	{

		headerGroup = new Group (parent, SWT.BORDER);
		headerGroup.setText(Messages.getString("SheetCTabItem.header")); //$NON-NLS-1$
		headerGroup.setLayout( new FormLayout() );
			
		
		//********		
		// Label Appname
		Label labelAppname = new Label(headerGroup, SWT.LEFT);
		labelAppname.setText(Messages.getString("SheetCTabItem.applicationLabel")); //$NON-NLS-1$
		labelAppname.setBackground(JQConst.backGround_JQEditor);
		labelAppname.pack();
		FormData formDataLabelAppname= new FormData();
		formDataLabelAppname.top = new FormAttachment(headerGroup,5);
		formDataLabelAppname.left = new FormAttachment(headerGroup,5);
		formDataLabelAppname.right = new FormAttachment(10,-5);
		labelAppname.setLayoutData(formDataLabelAppname);

		// Text Appname
		textAppname = new Text (headerGroup, SWT.SIMPLE |SWT.LEFT );
		FormData formDataTextAppname = new FormData();
		formDataTextAppname.top = new FormAttachment(0,5);
		formDataTextAppname.left = new FormAttachment(labelAppname,5,SWT.RIGHT);		
		formDataTextAppname.right= new FormAttachment(70,-5);
		textAppname.setLayoutData(formDataTextAppname);
		if (this.getSaveLibQSOS().getAppname()!= "") //$NON-NLS-1$
		{
			textAppname.setText(this.getSaveLibQSOS().getAppname());
		}
		
		
		//********		
		// Label Release
		Label labelRelease = new Label(headerGroup, SWT.LEFT);
		labelRelease.setText(Messages.getString("SheetCTabItem.ReleaseLabel")); //$NON-NLS-1$
		labelRelease.setBackground(JQConst.backGround_JQEditor);
		labelRelease.pack();
		FormData formDataLabelRelease= new FormData();
		formDataLabelRelease.top = new FormAttachment(textAppname,5,SWT.BOTTOM);
		formDataLabelRelease.left = new FormAttachment(0,5);
		formDataLabelRelease.right = new FormAttachment(10,-5);
		labelRelease.setLayoutData(formDataLabelRelease);
		

		// Text Release
		textRelease = new Text (headerGroup, SWT.SIMPLE | SWT.LEFT );
		FormData formDataTextRelease = new FormData();
		formDataTextRelease.top = new FormAttachment(labelAppname,5,SWT.BOTTOM);
		formDataTextRelease.left = new FormAttachment(labelRelease,5,SWT.RIGHT);		
		formDataTextRelease.right= new FormAttachment(70,-5);
		textRelease.setLayoutData(formDataTextRelease);
		if (this.getSaveLibQSOS().getRelease()!= "") //$NON-NLS-1$
		{
			textRelease.setText(this.getSaveLibQSOS().getRelease());
		}

		
		//********
		// Label Language
		Label labelLanguage = new Label(headerGroup,SWT.LEFT);
		labelLanguage.setText(Messages.getString("SheetCTabItem.languageLabel")); //$NON-NLS-1$
		labelLanguage.setBackground(JQConst.backGround_JQEditor);
		labelLanguage.pack();
		FormData formDataLabelLanguage = new FormData();
		formDataLabelLanguage.top = new FormAttachment(labelRelease,1,5);
		formDataLabelLanguage.left = new FormAttachment(0,5);
		formDataLabelLanguage.right = new FormAttachment(10,-5);
		labelLanguage.setLayoutData(formDataLabelLanguage);

		// Text language
		textLanguage = new Text ( headerGroup, SWT.SIMPLE|SWT.LEFT  );
		FormData formDataTextLanguage = new FormData();
		formDataTextLanguage.left = new FormAttachment(labelLanguage,5,SWT.RIGHT);
		formDataTextLanguage.top = new FormAttachment(labelRelease,5);
		formDataTextLanguage.right= new FormAttachment(70,-5);
		textLanguage.setLayoutData(formDataTextLanguage);
		if (getSaveLibQSOS().getLanguage()!= "") //$NON-NLS-1$
		{
			textLanguage.setText(this.getSaveLibQSOS().getLanguage());
		}

		
		//***************
		// Label License
		Label labelLicense = new Label(headerGroup, SWT.LEFT);
		labelLicense.setText(Messages.getString("SheetCTabItem.licenseLabel")); //$NON-NLS-1$
		labelLicense.setBackground(JQConst.backGround_JQEditor);
		labelLicense.pack();
		FormData formDataLabelLicense = new FormData();
		formDataLabelLicense.top = new FormAttachment(textLanguage,1);
		formDataLabelLicense.left = new FormAttachment(headerGroup,5);
		formDataLabelLicense.right = new FormAttachment(10,-5);
		labelLicense.setLayoutData(formDataLabelLicense);
		
		// Text License
		textLicense = new Text (headerGroup, SWT.SIMPLE | SWT.LEFT );
		FormData formDataTextLicense = new FormData();
		formDataTextLicense.top = new FormAttachment(textLanguage,5);
		formDataTextLicense.left = new FormAttachment(labelLicense,5,SWT.RIGHT);		
		formDataTextLicense.right= new FormAttachment(70,-5);
		textLicense.setLayoutData(formDataTextLicense);
		if (this.getSaveLibQSOS().getLicenseDesc()!= "") //$NON-NLS-1$
		{
			textLicense.setText(this.getSaveLibQSOS().getLicenseDesc());
		}

		
		
		//***************
		// Label URL
		Label labelUrl = new Label(headerGroup, SWT.LEFT);
		labelUrl.setText(Messages.getString("SheetCTabItem.urlLLabel")); //$NON-NLS-1$
		labelUrl.setBackground(JQConst.backGround_JQEditor);
		labelUrl.pack();
		FormData formDataLabelUrl = new FormData();
		formDataLabelUrl.top = new FormAttachment(textLicense,5);
		formDataLabelUrl.left = new FormAttachment(headerGroup,5);
		formDataLabelUrl.right = new FormAttachment(10,-5);
		labelUrl.setLayoutData(formDataLabelUrl);
		
		// Text URL
		textUrl = new Text (headerGroup, SWT.SIMPLE | SWT.LEFT);
		FormData formDataTextUrl = new FormData();
		formDataTextUrl.top = new FormAttachment(textLicense,5);
		formDataTextUrl.left = new FormAttachment(labelUrl,5,SWT.RIGHT);		
		formDataTextUrl.right= new FormAttachment(70,-5);
		textUrl.setLayoutData(formDataTextUrl);
		if (this.getSaveLibQSOS().getUrl()!= "") //$NON-NLS-1$
		{
			textUrl.setText(this.getSaveLibQSOS().getUrl());
		}

		
		
		
		//***************
		// Label Desc
		Label labelDesc = new Label(headerGroup, SWT.LEFT);
		labelDesc.setText(Messages.getString("SheetCTabItem.descriptionLabel")); //$NON-NLS-1$
		labelDesc.setBackground(JQConst.backGround_JQEditor);
		labelDesc.pack();
		FormData formDataLabelDesc = new FormData();
		formDataLabelDesc.top = new FormAttachment(textUrl,5);
		formDataLabelDesc.left = new FormAttachment(headerGroup,5);
		formDataLabelDesc.right = new FormAttachment(10,-5);
		labelDesc.setLayoutData(formDataLabelDesc);
		
		// Text Desc
		textDesc = new Text (headerGroup, SWT.MULTI | SWT.LEFT | SWT.MULTI);
		FormData formDataTextDesc = new FormData();
		formDataTextDesc.top = new FormAttachment(textUrl,5);
		formDataTextDesc.left = new FormAttachment(labelDesc,5,SWT.RIGHT);		
		formDataTextDesc.right= new FormAttachment(100,-5);
		textDesc.setLayoutData(formDataTextDesc);
		if (this.getSaveLibQSOS().getDesc()!= "") //$NON-NLS-1$
		{
			textDesc.setText(this.getSaveLibQSOS().getDesc());
		}
			
		
		
		
		//***************
		// Label Authors
		Label labelAuthors = new Label(headerGroup, SWT.LEFT);
		labelAuthors.setText(Messages.getString("SheetCTabItem.authorLabel")); //$NON-NLS-1$
		labelAuthors.setBackground(JQConst.backGround_JQEditor);
		labelAuthors.pack();
		FormData formDataLabelAuthors = new FormData();
	
		formDataLabelAuthors.left = new FormAttachment(headerGroup,5);
		formDataLabelAuthors.right = new FormAttachment(10,-5);
		formDataLabelAuthors.top = new FormAttachment(textDesc,5);
		labelAuthors.setLayoutData(formDataLabelAuthors);

		// Text Authors
		textAuthors = new Text ( headerGroup, SWT.SIMPLE | SWT.LEFT );
		FormData formDataTextAuthors = new FormData();
		formDataTextAuthors.left = new FormAttachment(headerGroup,5,SWT.RIGHT);
		formDataTextAuthors.right= new FormAttachment(50,-5);
		formDataTextAuthors.top = new FormAttachment(labelAuthors,5);
		textAuthors.setLayoutData(formDataTextAuthors);
		if (this.getSaveLibQSOS().getAuthors().size() > 0)
		{
			textAuthors.setText((String) this.getSaveLibQSOS().getAuthors().get(0));
		}
		
		// Text MailAuthors
		Label labelAuthorsMail = new Label(headerGroup, SWT.LEFT );
		labelAuthorsMail.setText(Messages.getString("SheetCTabItem.emailLabel")); //$NON-NLS-1$
		labelAuthorsMail.setBackground(JQConst.backGround_JQEditor);
		labelAuthorsMail.pack();
		FormData formDataLabelAuthorsMail = new FormData();
		formDataLabelAuthorsMail.left = new FormAttachment(textAuthors,5);
		formDataLabelAuthorsMail.right = new FormAttachment(100,-5);
		formDataLabelAuthorsMail.top = new FormAttachment(textDesc,5);
		labelAuthorsMail.setLayoutData(formDataLabelAuthorsMail);
		
		textAuthorsMail = new Text ( headerGroup, SWT.SIMPLE | SWT.LEFT);
		FormData formDataTextAuthorsMail = new FormData();
		formDataTextAuthorsMail.left = new FormAttachment(textAuthors,5,SWT.RIGHT);
		formDataTextAuthorsMail.right= new FormAttachment(100,-5);
		formDataTextAuthorsMail.top = new FormAttachment(labelAuthors,5);
		textAuthorsMail.setLayoutData(formDataTextAuthorsMail);
		if (this.getSaveLibQSOS().getAuthors().size() > 0)
		{
			textAuthorsMail.setText(this.getSaveLibQSOS().getEmail((String) this.getSaveLibQSOS().getAuthors().get(0)));
		}
		
		
		//***************
		// Label Image
		Label labelImage= new Label(headerGroup, SWT.BORDER | SWT.CENTER);
		ImageRegistryQSOS iR = new ImageRegistryQSOS();
		Image imageQSOS = iR.get(Messages.getString("SheetCTabItem.imageQSOSLabel")); //$NON-NLS-1$
		labelImage.setImage(imageQSOS);
		FormData formDataLabelImage = new FormData();
		formDataLabelImage.left = new FormAttachment (73,0);
		formDataLabelImage.right= new FormAttachment (98,0);
		formDataLabelImage.top = new FormAttachment (0,0);
		formDataLabelImage.bottom = new FormAttachment (labelDesc,-5,SWT.TOP);
		labelImage.setLayoutData(formDataLabelImage);
		labelImage.pack();
		

		// Add listener at textAppname
		textAppname.addKeyListener( new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.KEYCODE_ESCAPE:
						textAppname.setText (getSaveLibQSOS().getAppname());
						tableTreeViewer.refresh();
						textRelease.setFocus();
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				//nothing	
			}
			
		});
		textAppname.addVerifyListener( new VerifyListener()
		{
			public void verifyText(VerifyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.TAB_KEYCODE:
					case JQConst.ENTER_KEYCODE:
					case JQConst.ENTER_KEYCODE2:
						
						// Save the change on the sheet
						getSaveLibQSOS().setAppname(textAppname.getText());
						
						// Rename the tabulation
						if (saveLibQSOS.getAppname() != null)
						{
							if  (saveLibQSOS.getRelease() != null)
							{
								cTF.getSelection().setText ( saveLibQSOS.getAppname() + " " + saveLibQSOS.getRelease() ); //$NON-NLS-1$
							}
							else
							{
								cTF.getSelection().setText ( saveLibQSOS.getAppname() );
							}
						}
						else
						{
							// Cast a int to string
							cTF.getSelection().setText ( "" + cTF.getItemCount() ); //$NON-NLS-1$
						}
						//cTF.getSelection().setText(textAppname.getText());
						tableTreeViewer.refresh();
						arg0.doit = false;
						textRelease.setFocus();
						break;
				}				
			}
		});

		//Add listener at textRelease
		textRelease.addKeyListener( new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.KEYCODE_ESCAPE:
						textRelease.setText(getSaveLibQSOS().getRelease());
						tableTreeViewer.refresh();
						textLanguage.setFocus();
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				//nothing	
			}
			
		});
		textRelease.addVerifyListener( new VerifyListener()
		{
			public void verifyText(VerifyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.TAB_KEYCODE:
					case JQConst.ENTER_KEYCODE2:
					case JQConst.ENTER_KEYCODE:
						
						// Save change
						getSaveLibQSOS().setRelease(textRelease.getText());
							
						// Rename the tabulation
						if (saveLibQSOS.getAppname() != null)
						{
							if  (saveLibQSOS.getRelease() != null)
							{
								cTF.getSelection().setText( saveLibQSOS.getAppname() + " " + saveLibQSOS.getRelease() ); //$NON-NLS-1$
							}
							else
							{
								cTF.getSelection().setText ( saveLibQSOS.getAppname() );
							}
						}
						else
						{
							// Cast a int to string
							cTF.getSelection().setText ( "" + cTF.getItemCount() ); //$NON-NLS-1$
						}
						
						tableTreeViewer.refresh();
						arg0.doit = false;
						textLanguage.setFocus();
						break;
				}				
			}
		});

		// Add listener at textLanguage
		textLanguage.addKeyListener( new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.KEYCODE_ESCAPE:
						textLanguage.setText(getSaveLibQSOS().getLanguage());
						tableTreeViewer.refresh();
						textLicense.forceFocus();
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				//nothing	
			}
			
		});
		textLanguage.addVerifyListener(new VerifyListener()
		{
			public void verifyText(VerifyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.TAB_KEYCODE:
					case JQConst.ENTER_KEYCODE2:
					case JQConst.ENTER_KEYCODE:
						getSaveLibQSOS().setLanguage(textLanguage.getText());
						tableTreeViewer.refresh();
						arg0.doit = false;
						textLicense.forceFocus();
						break;
				}				
			}
		});

		
		//Add listener at textLicense
		textLicense.addKeyListener( new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.KEYCODE_ESCAPE:
						textLicense.setText(getSaveLibQSOS().getLicenseDesc());
						tableTreeViewer.refresh();
						textUrl.setFocus();
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				//nothing	
			}
			
		});
		textLicense.addVerifyListener( new VerifyListener()
		{
			public void verifyText(VerifyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.TAB_KEYCODE:
					case JQConst.ENTER_KEYCODE2:
					case JQConst.ENTER_KEYCODE:
						getSaveLibQSOS().setLicenseDesc(textLicense.getText());
						tableTreeViewer.refresh();
						arg0.doit = false;
						textUrl.setFocus();
						break;
				}				
			}
		});

		//Add listener at textUrl
		textUrl.addKeyListener( new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.KEYCODE_ESCAPE:
						textUrl.setText(getSaveLibQSOS().getUrl());
						tableTreeViewer.refresh();
						textDesc.forceFocus();
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				//nothing	
			}
			
		});
		textUrl.addVerifyListener( new VerifyListener()
		{
			public void verifyText(VerifyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.ENTER_KEYCODE2:
					case JQConst.ENTER_KEYCODE:
					case JQConst.TAB_KEYCODE:
						getSaveLibQSOS().setUrl(textUrl.getText());
						tableTreeViewer.refresh();
						arg0.doit = false;
						textDesc.forceFocus();
						break;
				}				
			}
		});

		//Add listener at textDesc
		textDesc.addKeyListener( new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.KEYCODE_ESCAPE:
						textDesc.setText(getSaveLibQSOS().getDesc());
						tableTreeViewer.refresh();
						textAuthors.setFocus();
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				//nothing	
			}
			
		});
		textDesc.addVerifyListener( new VerifyListener()
		{
			public void verifyText(VerifyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.TAB_KEYCODE:
					case JQConst.ENTER_KEYCODE2:
					case JQConst.ENTER_KEYCODE:
						getSaveLibQSOS().setDesc(textDesc.getText());
						tableTreeViewer.refresh();
						arg0.doit = false;
						textAuthors.setFocus();
						break;				
				}				
			}
		});

		//Add listener at textAuthors
		textAuthors.addKeyListener( new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.KEYCODE_ESCAPE:
						textAuthors.setText((String) getSaveLibQSOS().getAuthors().get(0));
						tableTreeViewer.refresh();
						//textAuthors.setFocus();
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				//nothing	
			}
			
		});
		textAuthors.addVerifyListener( new VerifyListener()
		{
			public void verifyText(VerifyEvent arg0)
			{
				if (cTF.getItemCount() > 0)
				{
					switch (arg0.keyCode)
					{
						case JQConst.ENTER_KEYCODE2:
						case JQConst.ENTER_KEYCODE:
						case JQConst.TAB_KEYCODE:
							tableTreeViewer.refresh();
							arg0.doit = false;
							textAuthorsMail.forceFocus();

					}	
				}
			}
		});
		
		//Add listener at textAuthors
		textAuthorsMail.addKeyListener( new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.KEYCODE_ESCAPE:
						textAuthorsMail.setText(getSaveLibQSOS().getEmail((String)getSaveLibQSOS().getAuthors().get(0)));
						tableTreeViewer.refresh();
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				//nothing	
			}
			
		});
		
		textAuthorsMail.addVerifyListener( new VerifyListener()
				{
					public void verifyText(VerifyEvent arg0)
					{
						if (cTF.getItemCount() > 0)
						{
							switch (arg0.keyCode)
							{
								case JQConst.ENTER_KEYCODE2:
								case JQConst.ENTER_KEYCODE:
								case JQConst.TAB_KEYCODE:
									getSaveLibQSOS().delAuthor((String) getSaveLibQSOS().getAuthors().get(0));
									getSaveLibQSOS().addAuthor(textAuthors.getText(),""); //$NON-NLS-1$
									tableTreeViewer.refresh();
									arg0.doit = false;
									boolean afterHeader = true;
									nextItem();

							}	
						}
					}
				});	

		headerGroup.setBackground(JQConst.backGround_JQEditor);
		activeGroup = headerGroup;

	}
	
	/**
	 * Method for create the answerComposite of this sheetCTabItem.
	 * The answer composite is the composite for help the user to complete the fields of the sheet
	 * 
	 * @param parent
	 */
	protected void createAnswerGroup(Composite parent)
	{	

		answerGroup = new Group (parent, SWT.BORDER | SWT.V_SCROLL | SWT.H_SCROLL);

		
		// Label Desc0
		buttonDesc0 = new Button (answerGroup,SWT.RADIO);
		buttonDesc0.setBackground(JQConst.backGround_JQEditor);
		FormData formDataButtonDesc0 = new FormData();
		formDataButtonDesc0.top = new FormAttachment(answerGroup,5);
		formDataButtonDesc0.left = new FormAttachment(answerGroup,5);
		formDataButtonDesc0.right = new FormAttachment(70,-5);
		buttonDesc0.setLayoutData(formDataButtonDesc0);
		
		// Button Desc1
		buttonDesc1 = new Button (answerGroup,SWT.RADIO);
		buttonDesc1.setBackground(JQConst.backGround_JQEditor);
		FormData formDataButtonDesc1 = new FormData();
		formDataButtonDesc1.top = new FormAttachment(buttonDesc0,5);
		formDataButtonDesc1.left = new FormAttachment(0,5);
		formDataButtonDesc1.right = new FormAttachment(70,-5);
		buttonDesc1.setLayoutData(formDataButtonDesc1);
			
		// Button Desc2
		buttonDesc2 = new Button (answerGroup,SWT.RADIO);
		buttonDesc2.setBackground(JQConst.backGround_JQEditor);
		FormData formDataButtonDesc2 = new FormData();
		formDataButtonDesc2.top = new FormAttachment(buttonDesc1,5);
		formDataButtonDesc2.left = new FormAttachment(0,5);
		formDataButtonDesc2.right = new FormAttachment(70,-5);
		buttonDesc2.setLayoutData(formDataButtonDesc2);

		// Label Comment	
		final Label labelComment = new Label(answerGroup, SWT.LEFT);
		labelComment.setBackground(JQConst.backGround_JQEditor);
		labelComment.setText(Messages.getString("SheetCTabItem.commentLabel")); //$NON-NLS-1$
		FormData formDataLabelComment = new FormData();
		formDataLabelComment.left = new FormAttachment(0,5);
		formDataLabelComment.top = new FormAttachment(buttonDesc2,30);
		labelComment.setLayoutData(formDataLabelComment);
		
		// Text Comment
		textComment = new Text ( answerGroup, SWT.MULTI | SWT.LEFT | SWT.BORDER);
		FormData formDataTextComment = new FormData();
		formDataTextComment.left = new FormAttachment(labelComment,5);
		formDataTextComment.right= new FormAttachment(100,-5);
		formDataTextComment.top = new FormAttachment(buttonDesc2,30);
		textComment.setLayoutData(formDataTextComment);

		// Label Score
		Label labelScore = new Label(answerGroup, SWT.LEFT);
		labelScore.setBackground(JQConst.backGround_JQEditor);
		labelScore.setText(Messages.getString("SheetCTabItem.scoreLabel")); //$NON-NLS-1$
		FormData formDataLabelScore = new FormData();
		formDataLabelScore.left = new FormAttachment(75,5);
		formDataLabelScore.right = new FormAttachment(100,-5);
		formDataLabelScore.top = new FormAttachment(0,5);
		labelScore.setLayoutData(formDataLabelScore);

		// Text Score
		textScore = new Text ( answerGroup, SWT.SIMPLE | SWT.CENTER | SWT.BORDER);
		FormData formDataTextScore = new FormData();
		formDataTextScore.top = new FormAttachment(labelScore,5, SWT.BOTTOM);
		formDataTextScore.left = new FormAttachment(labelScore, 40, SWT.LEFT);
		textScore.setTextLimit(1);	
		textScore.setLayoutData(formDataTextScore);

		buttonDesc0.addKeyListener(new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.ENTER_KEYCODE:
					case JQConst.ENTER_KEYCODE2:
						
						boolean afterHeader = false;
						textScore.setText(JQConst.SCORE_0);
						nextItem();
						
						if (buttonDesc0.getSelection())
						{
							buttonDesc0.forceFocus();
						}
						else if (buttonDesc1.getSelection())
						{
							buttonDesc1.forceFocus();
						}
						else if (buttonDesc2.getSelection())
						{
							buttonDesc2.forceFocus();
						}
						
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				// Nothing
			}
			
		});
		buttonDesc1.addKeyListener(new KeyListener()
		{
			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.ENTER_KEYCODE:
					case JQConst.ENTER_KEYCODE2:
						textScore.setText(JQConst.SCORE_1);
						boolean afterHeader = false;
						nextItem();
						if (buttonDesc0.getSelection())
						{
							buttonDesc0.forceFocus();
						}
						else if (buttonDesc1.getSelection())
						{
							buttonDesc1.forceFocus();
						}
						else if (buttonDesc2.getSelection())
						{
							buttonDesc2.forceFocus();
						}
						
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				// Nothing
			}
			
		});
		buttonDesc2.addKeyListener(new KeyListener()
		{

			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{

					case JQConst.ENTER_KEYCODE:
					case JQConst.ENTER_KEYCODE2:
						boolean afterHeader = false;
						textScore.setText(JQConst.SCORE_2);
						nextItem();
						if (buttonDesc0.getSelection())
						{
							buttonDesc0.forceFocus();
						}
						else if (buttonDesc1.getSelection())
						{
							buttonDesc1.forceFocus();
						}
						else if (buttonDesc2.getSelection())
						{
							buttonDesc2.forceFocus();
						}
						
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				// Nothing
			}
			
		});
		
		
		
		buttonDesc0.addSelectionListener( new SelectionListener()
		{

			public void widgetSelected(SelectionEvent arg0)
			{
				textScore.setText(JQConst.SCORE_0);
				try
				{
					element.setScore(textScore.getText());
					tableTreeViewer.refresh();
				} catch (IOException e)
				{
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}

			public void widgetDefaultSelected(SelectionEvent arg0)
			{
				// Nothing
			}
			
		});
		buttonDesc1.addSelectionListener( new SelectionListener(){

			public void widgetSelected(SelectionEvent arg0)
			{
				textScore.setText(JQConst.SCORE_1);
				try
				{
					element.setScore(textScore.getText());
					tableTreeViewer.refresh();
				} catch (IOException e)
				{
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				
			}

			public void widgetDefaultSelected(SelectionEvent arg0)
			{
				// Nothing
			}
			
		});
		
		buttonDesc2.addSelectionListener( new SelectionListener(){

			public void widgetSelected(SelectionEvent arg0)
			{
				textScore.setText(JQConst.SCORE_2);
				try
				{
					element.setScore(textScore.getText());
					tableTreeViewer.refresh();
				} catch (IOException e)
				{
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}

			public void widgetDefaultSelected(SelectionEvent arg0)
			{
				// Nothing
			}
			
		});
		
		textComment.addVerifyListener(new VerifyListener(){

			public void verifyText(VerifyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.TAB_KEYCODE:
					case JQConst.ENTER_KEYCODE:
					case JQConst.ENTER_KEYCODE2:
						
						// Save change
						element.setComment(textComment.getText());
							
						
						tableTreeViewer.refresh();
						arg0.doit = false;
						textScore.setFocus();
						nextItem();
						break;
				}	
				
				
			}
			
		});
		
		textComment.addKeyListener(new KeyListener()
		{

			public void keyPressed(KeyEvent arg0)
			{
				switch (arg0.keyCode)
				{
					case JQConst.KEYCODE_ESCAPE:
						textComment.setText(element.getComment());
						tableTreeViewer.refresh();
						textScore.setFocus();
						break;
				}
			}

			public void keyReleased(KeyEvent arg0)
			{
				// Nothing	
			}
			
		});
		
		textScore.addKeyListener(  new KeyListener()
				{
					public void keyPressed(KeyEvent arg0)
					{

						switch (arg0.keyCode)
						{
							case JQConst.UP_KEYCODE:
								lastItem();
								break;
							case JQConst.TAB_KEYCODE:
								//String text = textScore.getText();
								try
								{
									element.setScore(textScore.getText());
									tableTreeViewer.refresh();
								} catch (IOException e1)
								{
									// TODO Auto-generated catch block
									e1.printStackTrace();
								}

								textComment.forceFocus();
								break;
							
							case JQConst.KEYCODE_0:
							case JQConst.KEYCODE_0_BIS:
								textScore.setText("0"); //$NON-NLS-1$
								break;

							case JQConst.KEYCODE_1:
							case JQConst.KEYCODE_1_BIS:
								textScore.setText("1"); //$NON-NLS-1$
								break;

							case JQConst.KEYCODE_2:
							case JQConst.KEYCODE_2_BIS:
								textScore.setText("2"); //$NON-NLS-1$
								break;
													
							case JQConst.DOWN_KEYCODE:
							case JQConst.ENTER_KEYCODE2:
							case JQConst.ENTER_KEYCODE:
								try 
								{
									element.setScore(textScore.getText());
									tableTreeViewer.refresh();
								} catch (IOException e) 
								{
									e.printStackTrace();
								}
								boolean afterHeader = false;
									arg0.doit = false;
								// Go to the next item
								SheetCTabItem.this.nextItem();
								
								break;
						}
							
					}

					public void keyReleased(KeyEvent arg0)
					{
						//Nothing
					}
					
				});

		
		textScore.addVerifyListener ( new VerifyListener()
				{
					public void verifyText(VerifyEvent arg0)
					{
						if (arg0.text.equals("")) //$NON-NLS-1$
						{
							buttonDesc0.setSelection(false);
							buttonDesc1.setSelection(false);
							buttonDesc2.setSelection(false);
						}
						else if  (arg0.text.equals("0")) //$NON-NLS-1$
						{
							buttonDesc0.setSelection(true);
							buttonDesc1.setSelection(false);
							buttonDesc2.setSelection(false);
						}
						else if  (arg0.text.equals("1")) //$NON-NLS-1$
						{
							buttonDesc0.setSelection(false);
							buttonDesc1.setSelection(true);
							buttonDesc2.setSelection(false);
						}
						else if  (arg0.text.equals("2")) //$NON-NLS-1$
						{
							buttonDesc0.setSelection(false);
							buttonDesc1.setSelection(false);
							buttonDesc2.setSelection(true);
						}
						else if (arg0.text.equals("\t")) //$NON-NLS-1$
						{
							// Case when user pressed tab
							if (element.getScore().equals(JQConst.SCORE_0))
							{
								buttonDesc0.setSelection(true);
								buttonDesc1.setSelection(false);
								buttonDesc2.setSelection(false);
							}
							else if (element.getScore().equals(JQConst.SCORE_1))
							{
								buttonDesc0.setSelection(false);
								buttonDesc1.setSelection(true);
								buttonDesc2.setSelection(false);
							}
							else if (element.getScore().equals(JQConst.SCORE_2))
							{
								buttonDesc0.setSelection(false);
								buttonDesc1.setSelection(false);
								buttonDesc2.setSelection(true);
							}
							else
							{
								buttonDesc0.setSelection(false);
								buttonDesc1.setSelection(false);
								buttonDesc2.setSelection(false);
							}
						}
						else 
						{
							// No score is selected
							arg0.doit = false;
							buttonDesc0.setSelection(false);
							buttonDesc1.setSelection(false);
							buttonDesc2.setSelection(false);
						}
						
					}
					
				});
		


		
		

		
		
		answerGroup.setBackground(JQConst.backGround_JQEditor);
		
		// For set a nonActiveGroup (do not delete)
		nonActiveGroup = answerGroup;
		
	}
	

	
	
	
	/**
	 * Method for refresh the answerComposite of JQ application
	 * @param ielement
	 * 			It's the element which new answerGroup shows
	 * 			
	 * 
	 * @return
	 * 			return is the answerGroup
	 */
	public Composite refreshAnswerComposite(IElement ielement)
	{

		if (answerGroup.isEnabled())
		{
			// Set element (global Variable)
			element = ielement;

			// Set group's title
			if (element.getTitle()!= null)
			{
				answerGroup.setText(element.getTitle());
			}
			
			// Set desc0
			if (element.getDesc0() != null)
			{
				buttonDesc0.setText( element.getDesc0() );
			}
			else
			{
				buttonDesc0.setText(""); //$NON-NLS-1$
			}
			
			// Set desc1
			if (element.getDesc1() != null)
			{
				buttonDesc1.setText(element.getDesc1());
			}
			else
			{
				buttonDesc1.setText(""); //$NON-NLS-1$
			}
			
			// Set desc2
			if (element.getDesc2() != null)
			{
				buttonDesc2.setText(element.getDesc2());
			}
			else
			{
				buttonDesc2.setText(""); //$NON-NLS-1$
			}
			
			// Set Comment
			if (element.getComment() != null)
			{
				textComment.setText(element.getComment());
			}
			else
			{
				textComment.setText(""); //$NON-NLS-1$
			}
			
			// Set Score
			if (element.getScore() != null)
			{
				textScore.setText(element.getScore());
			}
			else
			{
				textScore.setText(""); //$NON-NLS-1$
			}
			
			// Set the selection of the radio button in function of the score
			if (element.getScore().equalsIgnoreCase(JQConst.SCORE_0))
			{
				buttonDesc0.setSelection(true);
			}
			else if (element.getScore().equalsIgnoreCase(JQConst.SCORE_1))
			{
				buttonDesc1.setSelection(true);
			}
			else if (element.getScore().equalsIgnoreCase(JQConst.SCORE_2))
			{
				buttonDesc2.setSelection(true);
			}
		}

		return answerGroup;

	}
	
	// First case for header
	/**
	 * This method give a group which is located on a anwerComposite of a jq application.
	 * It set the activeGroup last used and it location, and it size.
	 * 
	 * For indication: this method changed the activeGroup and the nonActiveGroup of the SheetCTabItem
	 * 
	 * @param choice
	 * 				if (choice = 1) 
	 * 						method return the headerGroup (so you can put element = null)
	 * 				else 
	 * 						method return the answerGroup completed with the element
	 * 
	 * @param element
	 * 				When the choice = 1 you can put null like element
	 * 				others case you must set the element which you want show
	 * 
	 * @return
	 * 				Method return a group place on the answerComposite
	 */
	public Composite giveComposite(int choice, IElement element)
	{
		
		if (choice != 0)
		{
			if (choice == 1)
			{
				nonActiveGroup = answerGroup;
				activeGroup = headerGroup;
			}
			else
			{

				refreshAnswerComposite(element);
				nonActiveGroup = headerGroup;
				activeGroup = answerGroup;
				
			}
		}
		
		if (activeGroup.getLayout() == null)
		{
			nonActiveGroup.setLayoutData(null);
			activeGroup.setLayout( new FormLayout() );
		}
		
		// Creation of a formData for AnswerComposite
		FormData formDataAnswerGroup = new FormData();

		
		// Set emplacement of the answerComposite
		formDataAnswerGroup.top = new FormAttachment(cTF,0,SWT.TOP);
		formDataAnswerGroup.left = new FormAttachment(cTF,0,SWT.LEFT);
		formDataAnswerGroup.bottom = new FormAttachment(jqApp.getAnswerComposite(),0,SWT.BOTTOM);
		formDataAnswerGroup.right = new FormAttachment(cTF,0,SWT.RIGHT);
		
		//Attribuate a formData to the answerComposite
		activeGroup.setLayoutData(formDataAnswerGroup);

		// Force the activeGroup' size 
		jqApp.getAnswerComposite().setSize(cTF.getSize().x,jqApp.getAnswerComposite().getSize().y);
		activeGroup.setSize(jqApp.getAnswerComposite().getSize().x-1,jqApp.getAnswerComposite().getSize().y-1);

		
		
		// Hide the onActiveGroup
		nonActiveGroup.setVisible(false);
		activeGroup.setVisible(true);
		
		
		// Set the focus on the element selected
		if (activeGroup.equals(answerGroup))
		{
			textScore.forceFocus();
			refreshAnswerComposite(element);
		}
		else
		{
			textAppname.forceFocus();
		}
		
		activeGroup.update();
		
		return activeGroup;		
	}

	
	/**
	 * 
	 * This method select the next Item to complete in the tableTreeViewer.
	 * It not selected simples titles, or section name.
	 * 
	 */
	private void nextItem ()
	{
		
		int selectionTable = jqApp.getTableTreeViewer().getTableTree().getTable().getSelectionIndex();
		
		jqApp.getTableTreeViewer().getTableTree().getTable().setFocus();
		jqApp.getAnswerComposite().update();
		
		jqApp.getTableTreeViewer().getTableTree().getTable().setSelection(selectionTable+1);
		
		ISelection select = jqApp.getTableTreeViewer().getSelection();
		
		boolean itemNotFound=true;

		TableTreeItem[] item ;
		int itemCountMax = jqApp.getTableTreeViewer().getTableTree().getTable().getItems().length;

		selectionTable = jqApp.getTableTreeViewer().getTableTree().getTable().getSelectionIndex()+1;
		IElement elementSelected;
		
		
		while (itemNotFound)
		{
			select = jqApp.getTableTreeViewer().getSelection();
			StructuredSelection structuredSelectionTransition = (StructuredSelection) select; 
			Object objectTransition = structuredSelectionTransition.getFirstElement();
			elementSelected = (IElement) objectTransition;
			
			selectionTable = jqApp.getTableTreeViewer().getTableTree().getTable().getSelectionIndex();
			
			if (selectionTable  < 0)
			{
				jqApp.getTableTreeViewer().getTableTree().getTable().setSelection(0);
			}
			else if (selectionTable == 0)
			{
				jqApp.getTableTreeViewer().getTableTree().getTable().setSelection(1);
			}
			else
			{
			
				if (elementSelected.getElements() == null)
				{
					select = jqApp.getTableTreeViewer().getSelection();
					jqApp.getTableTreeViewer().setSelection(select);
					itemNotFound = false;
				}
				else
				{

					selectionTable++;

					jqApp.getTableTreeViewer().getTableTree().getTable().setSelection(selectionTable);

				}
			}

		}
	}
	
	
	/**
	 * 
	 * This method select the last Item to complete in the tableTreeViewer.
	 * It not selected simples titles, or section name.
	 * 
	 */
	private void lastItem ()
	{
		
		int selectionTable = jqApp.getTableTreeViewer().getTableTree().getTable().getSelectionIndex();
		
		jqApp.getTableTreeViewer().getTableTree().getTable().setFocus();
		jqApp.getAnswerComposite().update();
		
		jqApp.getTableTreeViewer().getTableTree().getTable().setSelection(selectionTable-1);
		
		ISelection select = jqApp.getTableTreeViewer().getSelection();
		
		boolean itemNotFound=true;

		TableTreeItem[] item ;
		int itemCountMax = jqApp.getTableTreeViewer().getTableTree().getTable().getItems().length;

		selectionTable = jqApp.getTableTreeViewer().getTableTree().getTable().getSelectionIndex()-1;
		IElement elementSelected;
		
		
		while (itemNotFound)
		{
			select = jqApp.getTableTreeViewer().getSelection();
			StructuredSelection structuredSelectionTransition = (StructuredSelection) select; 
			Object objectTransition = structuredSelectionTransition.getFirstElement();
			elementSelected = (IElement) objectTransition;
			
			selectionTable = jqApp.getTableTreeViewer().getTableTree().getTable().getSelectionIndex();
			
			if (selectionTable  < 0 )
			{
				jqApp.getTableTreeViewer().getTableTree().getTable().setSelection(itemCountMax-1);
			}
			else if (selectionTable == 0)
			{
				jqApp.getTableTreeViewer().getTableTree().getTable().setSelection(0);
				giveComposite(1,null);
				itemNotFound = false;
			}
			else
			{
			
				if (elementSelected.getElements() == null)
				{
					select = jqApp.getTableTreeViewer().getSelection();
					jqApp.getTableTreeViewer().setSelection(select);
					itemNotFound = false;
				}
				else
				{

					selectionTable--;

					jqApp.getTableTreeViewer().getTableTree().getTable().setSelection(selectionTable);

				}
			}

		}
	}
	

	/**
	 * This method get the LibQSOS variable.
	 * When you changed a field in the tableTreeViewer, it was save in this LibQSOS.
	 * This LibQSOS have no header, which was delete in open, just an element named "Header"
	 * with no children
	 * 
	 * 
	 * @return libQSOS
	 * 
	 */
	public LibQSOS getLibQSOS()
	{
		return libQSOS;
	}


	/**
	 * This method set the LibQSOS variable.
	 * When you changed a field in the tableTreeViewer, it was save in this LibQSOS.
	 * This LibQSOS have no header, which was delete in open, just an element named "Header"
	 * with no children
	 * 
	 * 
	 * @param libQSOS
	 */
	public void setLibQSOS(LibQSOS libQSOS)
	{
		this.libQSOS = libQSOS;
	}
	
	/**
	 * This method get the saveLibQSOS variable.
	 * When you changed a field in the tableTreeViewer, it was save in this LibQSOS.
	 * This LibQSOS have no header, which was delete in open, just an element named "Header"
	 * with no children
	 * 
	 * 
	 * @return saveLibQSOS
	 */
	public LibQSOS getSaveLibQSOS()
	{
		return saveLibQSOS;
	}

	/**
	 * This method set the saveLibQSOS variable.
	 * When you changed a field in the tableTreeViewer, it was NOT save in this saveLibQSOS.
	 * Only all informations of the header was save and load in this variable.
	 * When the user saves, all informations of libQSOS was saved in saveLibQSOS.
	 * 
	 * 
	 * @param saveLibQSOS 
	 */
	public void setSaveLibQSOS(LibQSOS saveLibQSOS)
	{
		this.saveLibQSOS = saveLibQSOS;
	}
	


	
	
	/**
	 * This method return an AnswerGroup.
	 * An answerGroup is a group with fields to complete (or precomplete).
	 * It was created for help to complete the tableTreeViewer.
	 * 
	 * When you change the selection in a tableTreeViewer answer is not recreated but recompleted with the new element.
	 * AnswerGroup have keyListener for simplification of navigation in tableTreeViewer:
	 * 		Enter: for go to next score
	 * 		Escape: for cancel the text writed
	 * 		Up/down: for navigate in the tableTreeViewer (like enter)
	 * 		Tab:	for complete the comment
	 *  	
	 * 
	 * @return Returns the answerGroup.
	 */
	public Group getAnswerGroup() 
	{
		return answerGroup;
	}
	/**
	 * This method set the AnswerGroup.
	 * An answerGroup is a group with fields to complete (or precomplete).
	 * It was created for help to complete the tableTreeViewer.
	 * 
	 * When you change the selection in a tableTreeViewer answer is not recreated but recompleted with the new element.
	 * AnswerGroup have keyListener for simplification of navigation in tableTreeViewer:
	 * 		Enter: for go to next score
	 * 		Escape: for cancel the text writed
	 * 		Up/down: for navigate in the tableTreeViewer (like enter)
	 * 		Tab:	for complete the comment
	 *  	
	 * 
	 * 
	 * @param answerGroup
	 * 					The answerGroup to set.
	 */
	public void setAnswerGroup(Group answerGroup) 
	{
		this.answerGroup = answerGroup;
	}
	
	
	
	
	/**
	 * This method get the headerGroup.
	 * A headerGroup is a group with fields, relative of the header, to complete (or precomplete).
	 * It was created for help to complete the tableTreeViewer.
	 * 
	 * headerGroup have keyListener for simplification of navigation in tableTreeViewer:
	 * 		Enter or Tab: for go to next field
	 * 		Escape: for cancel the text writed
	 *  
	 * 
	 * @return Returns the headerGroup.
	 */
	public Group getHeaderGroup() 
	{
		return headerGroup;
	}
	
	/**
	 * This method set the headerGroup.
	 * A headerGroup is a group with fields, relative of the header, to complete (or precomplete).
	 * It was created for help to complete the tableTreeViewer.
	 * 
	 * headerGroup have keyListener for simplification of navigation in tableTreeViewer:
	 * 		Enter or Tab: for go to next field
	 * 		Escape: for cancel the text writed
	 *  
	 * 
	 * 
	 * @param headerGroup
	 */
	public void setHeaderGroup(Group headerGroup) {
		this.headerGroup = headerGroup;
	}

	/**
	 * Method for set the tableTreeViewer of the sheetCTabItem
	 * 
	 * @return tableTreeViewer
	 */
	public TableTreeViewer getTableTreeViewer()
	{
		return tableTreeViewer;
	}

	/**
	 * Method for get the tableTreeViewer of the sheetCTabItem
	 * 
	 * @param tableTreeViewer
	 */
	public void setTableTreeViewer(TableTreeViewer tableTreeViewer)
	{
		this.tableTreeViewer = tableTreeViewer;
	}


	/**
	 * Method for reinitialize the sheet :
	 * only the fields of libQSOS are reinitialized.
	 * 
	 * @param answer
	 * 			answer is a array of 3 booleans
	 * 				when answer[0] is true the scores of the sheet are reinitialized
	 * 				when answer[0] is true the comments of the sheet are reinitialized
	 * 	 			when answer[0] is true the header of the sheet are reinitialized  
	 */				
	public void reInitialized(boolean answer[])
	{
		
		// Set the first element to the header
		tableTreeViewer.getTableTree().getTable().setSelection(1);
		
		boolean scoreSelected = answer[0];
		boolean commentSelected = answer[1];
		boolean headerSelected = answer[2];
		
		// Declaration
		int selectionTable;
		ISelection select ;
		TableTreeItem[] item ;
		IElement elementSelected;
		
		// Force focus on the tableTreeViewer
		tableTreeViewer.getTableTree().getTable().setFocus();

		
		int itemCountMax = tableTreeViewer.getTableTree().getTable().getItems().length;

		String text;
		do
		{
			select = tableTreeViewer.getSelection();
			StructuredSelection structuredSelectionTransition = (StructuredSelection) select; 
			Object objectTransition = structuredSelectionTransition.getFirstElement();
			elementSelected = (IElement) objectTransition;
			
			selectionTable = tableTreeViewer.getTableTree().getTable().getSelectionIndex();
			
			if ( (selectionTable > 0) && (selectionTable < itemCountMax) )
			{
				if (elementSelected != null)
				{
					try
					{
						if (scoreSelected)
						{
							elementSelected.setScore(""); //$NON-NLS-1$
						}
						if (commentSelected)
						{
							elementSelected.setComment(""); //$NON-NLS-1$
						}
						
					} catch (IOException e)
					{
						System.out.println(Messages.getString("SheetCTabItem.ErrorInitializeScoreComment")); //$NON-NLS-1$
						e.printStackTrace();
					}
					
					
				}
				selectionTable++;
				tableTreeViewer.getTableTree().getTable().setSelection(selectionTable);

			}

		}while ((selectionTable > 0)&&(selectionTable < itemCountMax));
		
		if (headerSelected)
		{
			textLanguage.setText(""); //$NON-NLS-1$
			//textAppname.setText(""); //$NON-NLS-1$
			textRelease.setText(""); //$NON-NLS-1$
			textLicense.setText(""); //$NON-NLS-1$
			textDesc.setText(""); //$NON-NLS-1$
			textUrl.setText(""); //$NON-NLS-1$
			textAuthors.setText(""); //$NON-NLS-1$
			textAuthorsMail.setText(""); //$NON-NLS-1$
		}
		
		tableTreeViewer.getTableTree().getTable().setSelection(0);
		
		tableTreeViewer.refresh();
		
	}
}
