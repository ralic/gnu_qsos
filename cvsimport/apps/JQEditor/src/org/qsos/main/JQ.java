/*
**  $Id: JQ.java,v 1.2 2006/08/07 09:08:46 rpelisse Exp $
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
package org.qsos.main;


import org.eclipse.jface.action.CoolBarManager;
import org.eclipse.jface.action.Separator;
import org.eclipse.jface.action.ToolBarManager;
import org.eclipse.jface.viewers.CellEditor;
import org.eclipse.jface.viewers.ISelection;
import org.eclipse.jface.viewers.ISelectionChangedListener;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.jface.viewers.SelectionChangedEvent;
import org.eclipse.jface.viewers.TableTreeViewer;
import org.eclipse.jface.viewers.TextCellEditor;
import org.eclipse.jface.viewers.TreeViewer;
import org.eclipse.jface.window.ApplicationWindow;
import org.eclipse.swt.SWT;
import org.eclipse.swt.custom.CTabFolder;
import org.eclipse.swt.custom.TableTreeItem;
import org.eclipse.swt.events.DisposeEvent;
import org.eclipse.swt.events.DisposeListener;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.layout.FormAttachment;
import org.eclipse.swt.layout.FormData;
import org.eclipse.swt.layout.FormLayout;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Control;
import org.eclipse.swt.widgets.Display;
import org.eclipse.swt.widgets.Event;
import org.eclipse.swt.widgets.Listener;
import org.eclipse.swt.widgets.Shell;
import org.eclipse.swt.widgets.Table;
import org.eclipse.swt.widgets.TableColumn;
import org.qsos.action.OpenSheetAction;
import org.qsos.action.PrintAction;
import org.qsos.action.ReinitializeFieldsAction;
import org.qsos.action.SaveSheetAction;
import org.qsos.action.RadarAction;
import org.qsos.data.IElement;
import org.qsos.data.ImageRegistryQSOS;
import org.qsos.data.JQConst;
import org.qsos.data.Messages;
import org.qsos.interfaces.SheetCTabItem;
import org.qsos.interfaces.SheetCellModifier;
import org.qsos.interfaces.SheetTableTreeContentProvider;
import org.qsos.interfaces.SheetTableTreeLabelProvider;
import org.qsos.interfaces.SheetTreeContentProvider;
import org.qsos.interfaces.SheetTreeLabelProvider;
import org.qsos.utils.LibQSOS;




/**
 * @author MODELIN_M
 *
 * JQ is the main class of the JQEditor program.
 * This class is extends of ApplicationWindow.
 * 
 */
public class JQ extends ApplicationWindow 
{
	// This application
	private JQ jq;
	
	
	// Variables for Actions
	private OpenSheetAction openSheetAction;
	private RadarAction radarAction;
	private SaveSheetAction saveSheetAction;
	private PrintAction printAction;
	private ReinitializeFieldsAction reinitializeFieldsAction;
	

	// main TabFolder for the sheet's view
	private CTabFolder cTabFolder;
	
	// TableTreeViewer
	private TableTreeViewer tableTreeViewer;
	
	// 
	public ImageRegistryQSOS imageRegistry;
	
	// TreeViewer
	private TreeViewer treeViewer;
	
	// Composite for the treeView
	private Composite treeViewComposite;
	
	// Composite for the answer
	private Composite answerComposite;

	

	
	/**
	 * Contructor: add a ToolBar and a StatusLine
	 * 
	 * @param parentShell
	 * 					Shell
	 */
	public JQ(Shell parentShell) 
	{
		super(parentShell);
		imageRegistry = new ImageRegistryQSOS();
		
		addStatusLine();
		addToolBar(SWT.BORDER);
	}
	
	
	
	
	/**
	 * Contructor
	 */
	public JQ()
	{
		super(null);
        System.out.println("JQ is being run");
		
		addStatusLine();
		addCoolBar(SWT.BORDER);
	}
	
	/**
	 * Method for run the JQ application.
	 * 
	 */
	public void run()
	{
		// Don't return from open() until window closes
		setBlockOnOpen(true);
		
		// Open the main window
		open();
		
		// Dispose the display
		Display.getCurrent().dispose();
	}
	
	
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.window.Window#configureShell(org.eclipse.swt.widgets.Shell)
	 */
	protected void configureShell(Shell shell)
	{
		super.configureShell(shell);
		
		shell.setBackground(JQConst.backGround_JQEditor);
		
		shell.setText(Messages.getString("JQ.nameEditor")); //$NON-NLS-1$
		
		getCoolBarManager().getControl().setSize(80,30);
		
		imageRegistry = new ImageRegistryQSOS();
		
		shell.setImage( imageRegistry.get(Messages.getString("JQ.iconApplication"))); //$NON-NLS-1$
		
		
		// Initialize action
		openSheetAction = new OpenSheetAction(this);
		radarAction = new RadarAction(this);
		saveSheetAction = new SaveSheetAction(this);
		printAction = new PrintAction(this);
		reinitializeFieldsAction = new ReinitializeFieldsAction(this);
		
		// Initialize jq
		jq = this;

	}	
	
	
	
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.window.ApplicationWindow#createToolBarManager(int)
	 */
	protected ToolBarManager createToolBarManager(int style)
	{

		// Initialize Action
		//-------------------
		openSheetAction = new OpenSheetAction(this);
		radarAction = new RadarAction(this);
		saveSheetAction = new SaveSheetAction(this);
		printAction = new PrintAction(this);
		reinitializeFieldsAction = new ReinitializeFieldsAction(this);
		

		ToolBarManager toolBarManager = new ToolBarManager(style);

		toolBarManager.add(new Separator());
		toolBarManager.add(openSheetAction);
		toolBarManager.add(saveSheetAction);
		toolBarManager.add(printAction);
		toolBarManager.add(new Separator());
		toolBarManager.add(radarAction);
		toolBarManager.add(new Separator());
		toolBarManager.add(reinitializeFieldsAction);
		
		return toolBarManager;
	}
	
	
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.window.ApplicationWindow#createCoolBarManager(int)
	 */
	protected CoolBarManager createCoolBarManager(int style)
	{
		
		CoolBarManager cool_bar_manager = new CoolBarManager(SWT.LEFT);
		
		cool_bar_manager.add(createToolBarManager(SWT.LEFT));
		
		return cool_bar_manager;
	}
	
	
	
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.window.Window#createContents(org.eclipse.swt.widgets.Composite)
	 */
	protected Control createContents(Composite parent)
	{
		parent.setBackground(JQConst.backGround_JQEditor);
			
		getShell().setMaximized(true);
		
		// Creation of a FormLayout for organize the main Window
		FormLayout mainFormLayout = new FormLayout();
		parent.setLayout(mainFormLayout);
		
		
		// Attribuate a formData to the CoolBar for set its emplacement
		FormData formDataCoolBar = new FormData();
		getCoolBarControl().setLayoutData(formDataCoolBar);
		
		// Set the emplacement of the top of CoolBar
		formDataCoolBar.top = new FormAttachment(0,5);
		
		// We create the main CTabFolder
		createCTabFolder(parent);	
		

		// We create the TreeViewer
		createTreeViewer(parent);
		
		// Create the window for the answer
		createAnswerComposite(parent);
		
		return parent;
		
	}
	
	
	/**
	 * 
	 * This class create the answerComposite
	 * It s a window under the main window. 
	 * It permits to the user to complete the fields of sheet
	 * 
	 * @param parent
	 * 				composite where the window is visible 
	 * 				
	 */
	protected void createAnswerComposite( Composite parent )
	{
		
		// Create the new Composite for the AnswerWindow
		answerComposite = new Composite(parent, SWT.BORDER);
		
		// Creation of a formData for AnswerComposite
		FormData formDataAnswerComposite = new FormData();
		
		// Attribuate a formData to the CTabFolder
		answerComposite.setLayoutData(formDataAnswerComposite);
		
		// Set the emplacement of the CTabFolder
		formDataAnswerComposite.top = new FormAttachment(cTabFolder,5);
		formDataAnswerComposite.left = new FormAttachment(treeViewComposite,5);
		formDataAnswerComposite.bottom = new FormAttachment(100,-5);
		formDataAnswerComposite.right = new FormAttachment(100,-5);
		
		
		// Set color for the AnswerComposite
		answerComposite.setBackground(JQConst.backGround_JQEditor);		
	}
	
	
	
	/**
	 * 
	 * Method for refresh the AnswerComposite of the JQ application 
	 * 
	 */
	public void refreshAnswerComposite()
	{
		//Creation of a formData for AnswerComposite
		FormData formDataAnswerComposite = new FormData();
		
		// Attribuate a formData to the CTabFolder
		answerComposite.setLayoutData(formDataAnswerComposite);
		
		// Set emplacement of the CTabFolder
		formDataAnswerComposite.top = new FormAttachment(cTabFolder,5,SWT.BOTTOM);
		formDataAnswerComposite.left = new FormAttachment(treeViewComposite,5,SWT.RIGHT);
		formDataAnswerComposite.bottom = new FormAttachment(100,-5);
		formDataAnswerComposite.right = new FormAttachment(cTabFolder,-5,SWT.RIGHT);
		

	}
	
	
	/**
	 * This class create the CTabFolder in a composite with good's properties
	 * The CTabItem contains properties of sheet 
	 * Two listeners are implemented: one for Selection and an other for resize. 
	 * 
	 * 
	 * @param parent
	 * 				composite where the CTabFolder is visible
	 */
	protected void createCTabFolder(final Composite parent)
	{
		// Attribuate a formData to the CTabFolder
		FormData formDataTableFolder = new FormData();
		
		// Create a CTabFolder
		cTabFolder = new CTabFolder(parent, SWT.CLOSE | SWT.FLAT | SWT.BORDER);
		
		// Set same height between buttons of coolBar and the TabFolder 
		cTabFolder.setTabHeight(getCoolBarManager().getControl().getSize().y);
		
		// Style of the tabulation
		cTabFolder.setSimple(false);
		cTabFolder.setBorderVisible(false);
		
		// Attribuate a FormLayout to a CTabFolder for set his emplacement
		cTabFolder.setLayoutData(formDataTableFolder);
		
		//Put the border of cTabFolder visible
		cTabFolder.setBorderVisible(true);
		
		// Set emplacement of the CTabFolder
		formDataTableFolder.top = new FormAttachment(0,5);
		formDataTableFolder.left = new FormAttachment(getCoolBarControl(),5);
		formDataTableFolder.bottom = new FormAttachment(75,-5);
		formDataTableFolder.right = new FormAttachment(100,-5);
		
		// Set color of the background
		cTabFolder.setBackground(JQConst.backGround_JQEditor);
		
		// Set the color for the CTabItem selected
		cTabFolder.setSelectionBackground(JQConst.backGround_JQEditor3);
		
		// Put the "close" of cTabFolder unselected unvisible 
		cTabFolder.setUnselectedCloseVisible(false);
		
		
		cTabFolder.addListener(SWT.Resize, new Listener()
				{
			public void handleEvent(Event e)
			{
				switch (e.type)
				{
				case SWT.Resize:
					if (cTabFolder != null)
					{
						if (cTabFolder.getItemCount() > 0)
						{	
							refreshTableTreeViewer();
							refreshAnswerComposite();
							((SheetCTabItem)cTabFolder.getSelection()).giveComposite(0,null);
							
						}
					}
				break;
				}
			}
				});
		


		cTabFolder.addSelectionListener( new SelectionListener()
		{
			
			public void widgetSelected(SelectionEvent arg0)
			{
				// Case when you select a tab
				if (cTabFolder.getItemCount()>0)
				{
					// Refresh the treeViewer
					refreshTreeViewer();
									
					// Set all answerGroup hide
					for (int i=0; i< cTabFolder.getItemCount();i++)
					{
						((SheetCTabItem)cTabFolder.getItem(i)).getHeaderGroup().setVisible(false);
						((SheetCTabItem)cTabFolder.getItem(i)).getAnswerGroup().setVisible(false);
					}
					
					// Set the answerGroup of the tab selected visible
					((SheetCTabItem) cTabFolder.getSelection()).getHeaderGroup().setVisible(true);	
					
				}
			}
	
			
			public void widgetDefaultSelected(SelectionEvent arg0)
			{
				// Nothing	
			}
	
		});
	}
	
	
	
	/**
	 * 
	 * Method for create the TableTreeViewer with good properties and size.
	 * 	A listener of selection is implemented (type ISelectionChangedListener)
	 * 	This listener refreshs the AnswerComposite and the treeViewer  
	 * 
	 * @param parent
	 * 				Composite where the TableTreeViewer is show
	 * 
	 * @param sheetCTabItem
	 * 				TabItem of the CTabFolder where's sheet is open
	 * 
	 * @return
	 * 				Control of tableTreeViewer
	 */
	protected Control createTableTreeViewer(Composite parent, SheetCTabItem sheetCTabItem)
	{
		// Create the table viewer to display the sheet
		tableTreeViewer = new TableTreeViewer(parent);
		tableTreeViewer.getTableTree().setLayoutData(new GridData(GridData.FILL_BOTH));
		
		// Set the content and label providers
		tableTreeViewer.setContentProvider( new SheetTableTreeContentProvider( sheetCTabItem.getLibQSOS().getSheet() ));
		tableTreeViewer.setLabelProvider( new SheetTableTreeLabelProvider() );

		// Copy getRoot in element...
		IElement element = sheetCTabItem.getLibQSOS().getSheet().getRoot();
		
		// Set header NULL
		((IElement)element.getElements().get(0)).setElements(null);
		// DO NOT CHANGE THE TERME OF JQ.HEADER
		// IF YOU CHANGED SOMETHING CHANGE IN THE LISTENER OF tableTreeViewer some lines under
		((IElement)element.getElements().get(0)).setDesc(Messages.getString("JQ.header")); //$NON-NLS-1$
		
		
		
		tableTreeViewer.setInput(element);
		
		//sheetCTabItem.getLibQSOS().setSheet(sheetSave);
		//((IElement)sheetCTabItem.getLibQSOS().getSheet().getRoot()).addElement((IElement) sheetSave.getRoot().getElements().get(0));
		
		
		// Set up the table
		Table table = tableTreeViewer.getTableTree().getTable();
		new TableColumn(table,SWT.LEFT);	// Title column
		new TableColumn(table,SWT.LEFT);	// Desc0,1,2 Column
		new TableColumn(table,SWT.RIGHT);	// Comment Column
		new TableColumn(table,SWT.RIGHT);	// Score Column
		
		
		// Create the CellEditor
		CellEditor[] tableTreeEditor = new CellEditor[JQConst.MAX_COLUMN_NUMBER];
		
		// Loop for put TextCellEditor in the table
		int i = 0;
		for ( i = 0; i < JQConst.MAX_COLUMN_NUMBER; i++ )
		{
			tableTreeEditor[i] = new TextCellEditor(table);			
		}
		
		// Set the editor, cell modifier, and column properties
		String[] titleColumn = { Messages.getString("JQ.TitleColumn"),Messages.getString("JQ.DescColumn"), Messages.getString("JQ.CommentColumn"), Messages.getString("JQ.ScoreColumn") };
		tableTreeViewer.setColumnProperties(titleColumn);
		tableTreeViewer.setCellModifier( new SheetCellModifier( tableTreeViewer));
		tableTreeViewer.setCellEditors(tableTreeEditor);
		
		// Expand everything
		tableTreeViewer.expandAll();
		
		// Pack the columns
		for (i = 0; i < table.getColumnCount(); i++)
		{
			table.getColumn(i).setResizable(true);
			table.getColumn(i).setMoveable(false);
			
			// Put field's alignment on left side
			table.getColumn(i).setAlignment(SWT.LEFT);
			
			// Resize all columns
			switch (i)
			{
				case JQConst.COLUMN_TITLE:
					table.getColumn(i).setText(Messages.getString("JQ.TitleColumn")); //$NON-NLS-1$
					table.getColumn(i).getText().toUpperCase();			
					table.getColumn(i).setWidth(cTabFolder.getSize().x/JQConst.DENOMINATOR_WIDTH_COLUMN_TITLE );
				break;
				case JQConst.COLUMN_DESC:
					table.getColumn(i).setText(Messages.getString("JQ.DescColumn")); //$NON-NLS-1$
					table.getColumn(i).setWidth(cTabFolder.getSize().x/JQConst.DENOMINATOR_WIDTH_COLUMN_DESC );
				break;
				case JQConst.COLUMN_COMMENT:
					table.getColumn(i).setText(Messages.getString("JQ.CommentColumn")); //$NON-NLS-1$
					table.getColumn(i).setWidth(cTabFolder.getSize().x/JQConst.DENOMINATOR_WIDTH_COLUMN_COMMENT );
				break;
				case JQConst.COLUMN_SCORE:
					table.getColumn(i).setText(Messages.getString("JQ.ScoreColumn")); //$NON-NLS-1$
					table.getColumn(i).setWidth(cTabFolder.getSize().x/JQConst.DENOMINATOR_WIDTH_COLUMN_SCORE );
				break;
			}
		}
		
		
		// Turn off the header et turn on the lines
		table.setHeaderVisible(true);
		table.setLinesVisible(true);
		
		// Scroll to top
		tableTreeViewer.reveal(tableTreeViewer.getElementAt(0));
		
		
		//TODO make a method for put color on good section
		//Put color on item Header
		table.getItem(0).setBackground(JQConst.backGround_JQEditor);
		

		// Listener for selection on tableTreeViewer
		tableTreeViewer.addSelectionChangedListener( new ISelectionChangedListener()
		{
			public void selectionChanged(SelectionChangedEvent event) 
			{
				IStructuredSelection selection = (IStructuredSelection) event.getSelection();
				
				// Cast the selection on IElement
				IElement element = (IElement)selection.getFirstElement();
				
				if (element != null)
				{
					if ( element.getElements() == null)
					{
						if ( element.getDesc().equalsIgnoreCase(Messages.getString("JQ.header"))) //$NON-NLS-1$
						{
							if (cTabFolder.getItemCount()>0)
							{
								
								// Set all answerGroup hide
								for (int i = 0; i < cTabFolder.getItemCount();i++)
								{
									((SheetCTabItem)cTabFolder.getItem(i)).getHeaderGroup().setVisible(false);
									((SheetCTabItem)cTabFolder.getItem(i)).getAnswerGroup().setVisible(false);
								}
								
								// Set the answerGroup of the tab selected visible
								((SheetCTabItem) cTabFolder.getSelection()).getHeaderGroup().setVisible(true);						
							}
							
						}
						else
						{
							// Common case
							if (cTabFolder.getItemCount() > 0)
							{
								
								// Set all answerGroup hide
								for (int i = 0; i < cTabFolder.getItemCount();i++)
								{
									((SheetCTabItem)cTabFolder.getItem(i)).getHeaderGroup().setVisible(false);
									((SheetCTabItem)cTabFolder.getItem(i)).getAnswerGroup().setVisible(false);
								}
								
								
								//Set the answerGroup of the tab selected visible
								((SheetCTabItem) cTabFolder.getSelection()).refreshAnswerComposite(element);
								
								
								// Select the answerComposite (Header = 1; Answer != 1)
								int answerChoice = 2;
								((SheetCTabItem) cTabFolder.getSelection()).giveComposite(answerChoice,element).setVisible(true);
								
//								
								refreshAnswerComposite();

							}
						}
						
					}
				}
			}
		});
	

		return tableTreeViewer.getControl();
	}
	
	
	/**
	 * 
	 * Method for resize columns of the active tableTreeViewer
	 * 
	 */
	public void refreshTableTreeViewer ()
	{
		// Set up the table
		
		Table table = tableTreeViewer.getTableTree().getTable();
		table.update();
		// Pack the columns
		int i;
		for (i = 0; i < table.getColumnCount(); i++)
		{
			
			cTabFolder.update();
			//table.getColumn(i).pack();
			table.getColumn(i).setResizable(true);
			table.getColumn(i).setMoveable(false);
			
			// Put field's alignment on left side
			table.getColumn(i).setAlignment(SWT.LEFT);
			
			
			// Resize all columns
			switch (i)
			{
				case JQConst.COLUMN_TITLE:
					//table.getColumn(i).setText(JQConst.TITLE);
					table.getColumn(i).getText().toUpperCase();			
					table.getColumn(i).setWidth(cTabFolder.getSize().x/JQConst.DENOMINATOR_WIDTH_COLUMN_TITLE );
				break;
				case JQConst.COLUMN_DESC:
					//table.getColumn(i).setText(JQConst.DESC);
					table.getColumn(i).setWidth(cTabFolder.getSize().x/JQConst.DENOMINATOR_WIDTH_COLUMN_DESC );
				break;
				case JQConst.COLUMN_COMMENT:
					//table.getColumn(i).setText(JQConst.COMMENT);
					table.getColumn(i).setWidth(cTabFolder.getSize().x/JQConst.DENOMINATOR_WIDTH_COLUMN_COMMENT );
				break;
				case JQConst.COLUMN_SCORE:
					//table.getColumn(i).setText(JQConst.SCORE);
					table.getColumn(i).setWidth(cTabFolder.getSize().x/JQConst.DENOMINATOR_WIDTH_COLUMN_SCORE );
				break;
			}
			
		}
	}
	
	
	
	
	/**
	 * 
	 * Method for create the treeViewer
	 * 	This method set the BackGround'color, the formData and
	 *	set a listener on selection ( SelectionChangedListener )
	 *  
	 *  
	 * @param parent
	 * 				composite where's treeViewer is visible
	 * 
	 * 
	 */
	protected void createTreeViewer(Composite parent)
	{
		
		// Creation of a composite in the TreeViewer
		treeViewComposite = new Composite(parent,SWT.BORDER | SWT.FLAT);
		
		// Set the background with the color of JQEditor
		treeViewComposite.setBackground(JQConst.backGround_JQEditor);
		
		
		// Attribuate a layout to headerComposite
		treeViewComposite.setLayout(new FormLayout());
		
		// Creation of the FormData for the compositeTreeView
		FormData formDataTreeViewComposite = new FormData();
		
		// Attribuate a formData to the treeViewComposite
		treeViewComposite.setLayoutData(formDataTreeViewComposite);
		
		
		// Set position of treeViewComposite
		formDataTreeViewComposite.top = new FormAttachment(getCoolBarControl(),5,SWT.DOWN);
		formDataTreeViewComposite.left = new FormAttachment(0,5);
		formDataTreeViewComposite.right = new FormAttachment(cTabFolder,-5,SWT.LEFT);
		formDataTreeViewComposite.bottom = new FormAttachment(100,-5);		
		
		
		// Create the treeViewer
		treeViewer = new TreeViewer(treeViewComposite,SWT.LEFT);
		
		// Set the background of the treeVeiwer with the color of JQEditor
		treeViewer.getControl().setBackground(JQConst.backGround_JQEditor);
		
		// Create a formData for the treeViewer and attribuate it to the treeView
		FormData formDataTreeViewer = new FormData();
		treeViewer.getTree().setLayoutData(formDataTreeViewer);
		
		// Set position of treeVewer on the composite
		formDataTreeViewer.top = new FormAttachment(0,5);
		formDataTreeViewer.left = new FormAttachment(0,5);
		formDataTreeViewer.right = new FormAttachment(100,-5);
		formDataTreeViewer.bottom = new FormAttachment(100,-5);
		
		
		
		

		treeViewer.addSelectionChangedListener( new ISelectionChangedListener()
		{
			public void selectionChanged(SelectionChangedEvent arg0)
			{
				ISelection itemTreeSelection = treeViewer.getSelection();
				if ( cTabFolder.getItemCount() > 0)
				{
					if ( itemTreeSelection != null)
					{
						TableTreeViewer tTV = ((SheetCTabItem)cTabFolder.getSelection()).getTableTreeViewer();
						
						tTV.setSelection(itemTreeSelection);
					}
				}	
			}
			
		});
		
	}
	
	/**
	 * Method for refresh the treeViewer of the JQ application.
	 * 
	 *  
	 */
	public void refreshTreeViewer()
	{
		
		if (cTabFolder.getItemCount() > 0)
		{
			treeViewer.setContentProvider(new SheetTreeContentProvider(cTabFolder));
			treeViewer.setLabelProvider(new SheetTreeLabelProvider());
			IElement elementToInput = (((SheetCTabItem) cTabFolder.getSelection()).getLibQSOS().getSheet().getRoot());
			treeViewer.setInput(elementToInput);		
		}
		
	}
	

	
	/**
	 * Method for create a new Sheet
	 * This method is call by a button
	 * 
	 */
	public void createNewSheet ()
	{
		
	}
	
	/**
	 * 
	 * Method for open a sheet
	 * This method is called by the Open button
	 * 
	 * @param adress
	 * 				Adress URL of a sheet (depends of the system)
	 * 
	 */
	public void openSheet (String adress)
	{
		
		// Hide the answer composite for insert a new
		if (cTabFolder.getItemCount() > 0)
		{
			((SheetCTabItem) cTabFolder.getSelection()).getAnswerGroup().setVisible(false);
			((SheetCTabItem) cTabFolder.getSelection()).getHeaderGroup().setVisible(false);
		}
		final SheetCTabItem sheetCTabItem = new SheetCTabItem(cTabFolder,SWT.BORDER|SWT.CLOSE,adress,answerComposite,jq );
		
		
		sheetCTabItem.setControl(createTableTreeViewer(cTabFolder,sheetCTabItem));
		sheetCTabItem.setTableTreeViewer(tableTreeViewer);
		
		// Listener when the user close this cTabItem
		sheetCTabItem.addDisposeListener(new DisposeListener()
		{
			public void widgetDisposed(DisposeEvent arg0) 
			{
				IElement element = sheetCTabItem.getLibQSOS().getSheet().getRoot();
				element.getElements().set(0,sheetCTabItem.getSaveLibQSOS().getSheet().getRoot().getElements().get(0));
				
				sheetCTabItem.getAnswerGroup().dispose();
				sheetCTabItem.getHeaderGroup().dispose();
				treeViewer.setInput(null);
				treeViewComposite.update();
				
				if (cTabFolder.getItemCount() > 0)
				{
					refreshTreeViewer();
				}
				
			}
		});
			
	
		// Select the SheetCTabItem
		cTabFolder.setSelection(sheetCTabItem);
		
		// Set the HeaderGroup Visible
		int choiceHeaderIsVisible = 0;
		((SheetCTabItem) cTabFolder.getSelection()).giveComposite(choiceHeaderIsVisible,null);

		
		if ( cTabFolder.getItemCount() > 0 )
		{	
			refreshTreeViewer();
		}
		
		
		
	}
	
	
	/**
	 * Method for save the active sheet on a file
	 * 
	 * @param adressSaveFile
	 */
	public void saveSheet (String adressSaveFile)
	{
		LibQSOS lib = ((SheetCTabItem) cTabFolder.getSelection()).getLibQSOS();
		LibQSOS saveLib = ((SheetCTabItem) cTabFolder.getSelection()).getSaveLibQSOS();
		
		int numberSection = ((SheetCTabItem) cTabFolder.getSelection()).getSaveLibQSOS().getSheet().getRoot().getElements().size();
		for (int i = 1; i < numberSection; i++ )
		{
			saveLib.getSheet().getRoot().getElements().set(i,lib.getSheet().getRoot().getElements().get(i));
		}
		
		
		if (adressSaveFile != null)
		{
			saveLib.write(adressSaveFile);
		}
		
		

	}
	
	/**
	 * Method for get this jq Application
	 * 
	 * @return jq
	 */
	public JQ getApp()
	{
		return jq;
	}
	
	
	/**
	 * Method for get the cTabFolder of the JQ application.
	 * 
	 * @return cTabFolder
	 */
	public CTabFolder getCTabFolder()
	{
		
		return cTabFolder;
	}
	
	/**
	 * Method for set the cTabFolder of the jq application.
	 * 
	 * @param cTF
	 */
	public void setCTabFolder(CTabFolder cTF)
	{
		cTabFolder = cTF;
	}

	
	/**
	 * Main for launch the program
	 * 
	 * @param args
	 */
	public static void main(String[] args)
	{
		
		
		JQ jq = new JQ();
		
		jq.run();
		
		// Dispose
		jq.imageRegistry.dispose();
		jq.cTabFolder.dispose();
		jq.answerComposite.dispose();
		jq.treeViewComposite.dispose();
	}


	/**
	 * Method for get the tableTreeViewer of the CTabItem.
	 * 
	 * @return tableTreeViewer
	 */
	public TableTreeViewer getTableTreeViewer()
	{
		return tableTreeViewer;
	}


	/**
	 * Method for set the tableTreeViewer of the CTabItem.
	 * 
	 * @param tableTreeViewer
	 * 
	 */
	public void setTableTreeViewer(TableTreeViewer tableTreeViewer)
	{
		this.tableTreeViewer = tableTreeViewer;
	}


	/**
	 * Method for get the answerComposite of the JQ application
	 * 
	 * @return answerComposite
	 * 			Composite under the cTabFolder where the fields of the sheet are show
	 */
	public Composite getAnswerComposite()
	{
		return answerComposite;
	}


	/**
	 * Method for set the answerComposite of the JQ
	 * 
	 * @param answerComposite
	 * 			Composite under the cTabFolder where the fields of the sheet are show
	 */
	public void setAnswerComposite(Composite answerComposite)
	{
		this.answerComposite = answerComposite;
		
	}
	
	
	
}
