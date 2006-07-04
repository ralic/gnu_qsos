<?php
include("../../../func.php");

$lang = getlang();
head($lang);
?>
    <h1>QSOS HOWTO - How to design a new functional grid?</h1>

    Author : <a href="mailto:raphael AT semeteys DOT org">Raphaël Semeteys</a>

    <h3>Summary</h3>

    <p>This <i>howto</i> first exposes best practices when designing a the functional grid of a type of software not covered yet by QSOS. It then focuses on how to formalize that grid in the XML QSOS format and contibute it back to all.</p>

    <h2>Step 1 - Design the grid</h2>

    <p>Here comes a few best practices that we recommand you to consider when designing a new functional grid.</p>

    <h3>What to fo before starting</h3>

    <p>Check if agrid doesn't already exist ofor that type of software or for a similar type. The official QSOS website lists finalized grids (<a href="http://www.qsos.org/sheets/templates/">http://www.qsos.org/sheets/templates/</a>), but you should also check the project's CVS (<a href="http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos">http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos</a>).</p>

    <p>Inform the QSOS community of what you're about to, others might already work (or want to) on the same subject : just post a message on the QSOS mailing list qsos-general@nongnu.org (to subscribe : <a href="http://lists.nongnu.org/mailman/listinfo/qsos-general">http://lists.nongnu.org/mailman/listinfo/qsos-general</a>), or whenever not possible, send an email to coordinateur@qsos.org</p> 

    <p>Communicate in the mailing list on your difficulties, doubts or misunderstandings, you'll be given usefull advices, ideas or remarks.</p>

    <h3>Organize your grid</h3>

    <p>Always bear in mind that your grid might be used in contextes you don't know yet. So try to be as generic as possible.</p>

    <p>Regroup your criteria in hierachical levels, your grid will become more structured and readable. However, it is recommanded to limit the number of levels to 3 or 4 maximum.</p>

    <p>The structure often reveals itself as you design the grid, so do not panic if you don't have in minf the global view of it at the earluy beginning.</p>

    <p>Do not hesitate to reorganize the grid if it simplifies its reading and its use.</p>

    <p>Systematically look for already available information as well as your own expertise:</p>

    <ul>
      <li>Use the Internet to find existing comparisons which could give you new ideas and make you pinpoint criteria you didn't think of before yet.</li>
      <li>Check feature lists available on websites of type of sofwtare you are evaluating.</li>
      <li>Always consider several software when designing the functional grid. This will prevent you from building a grid for a specific product only.</li>
      <li>Also consider proprietary products! At this stage the most important objective is to make sure you didn't forget an important aspect of the grid on the way.</li>
    </ul>

    <p>Avoid irrelevant criteria (at least to your eyes), like:</p>

    <ul>
      <li>Subjectives criteria which will be difficult or even impossible to evaluate (like "Performances" for instance). If  you still miss such a criterion, it needs probably to be split in more objective subcriteria (for instance: "Ergonomics" could decome "Ergonomics/GUI global coherence", "Ergonomics/Help/General Help", "Ergonomics/Help/Contectual Help", etc.).</li>
      <li>Too precise crireria or criteria without real new value (like "Number of entries in the menu" for instance).</li>
      <li>Redundant criteria.</li>
    </ul>

    <h4>Non scored criteria</h4>

    <p>QSOS allows non scored criteria that still carry information and value the the grid.</p>

    <p>A common dilemna could be illustrated like this: "Should there be one lonely criterion enumarating all possible supported web browser, or should there rather be a single subcriterion per supported browser, the whole being grouped under a single upper-level criteria?".<p>
    <p>Each option has pros and cons, a best practise is to considee the middle path. In our example it could be: "Supported Web browsers/Internet Explorer" (to be scored), "Supported Web browsers/Mozilla Firefox" (to be scored), "Supported Web browsers/Safari" (to be scored), "Supported Web browsers/Konqueror" (to be scored) et "Supported Web browsers/Autres navigateurs" (non scored list).</p>

    <h2>Step 2 - Format the grid in a QSOS XML document</h2>

    <h3>Functional grids have to be formalized in the QSOS XML format to be usable.</h3>

    <p>You have for now three options to produce the XML document:</p>

    <ul>
      <li>You can directly write it with an text editor of your choice. You'll have to write the QSOS tags by yourself. A good practise is to resue an already existing XML grid.</li>
      <li>You can post your grid in clear text on the QSOS mailing list, somebody (another user or even a project admin) will probably format it for you.</li>
      <li>You can use a beta version of the "QSOS XUL Template Editor" tool to generate the file without entering any XML tag (<a href="http://cvs.savannah.nongnu.org/viewcvs/qsos/apps/tpl-xuleditor/?root=qsos">CVS access</a>). NB : the "xulrunner" framework from Mozilla must be installed on your computer for the editor to run.</li>
    </ul>

    <p>Whatever options you choose, do not forget to:</p>

    <ul>
      <li>Fill all the tags of the XML format (or provide information for somebody else to do it), more particularly the &lt;desc0&gt;, &lt;desc1&gt; and &lt;desc2&gt; tags since they convey the signification of your criteria's scores.</li>
      </li>
      <li>Specify header information like the software family, your name and email address, etc.</li>
    </ul>

    <!-- to be removed later -->
    <p>
    We known that for the time being this step is not very simple so do not hesitate to ask for help on the mailing list if you're stuck or lost.
    </p>

    <h2>Step 3 - Share your grid and follow its evolution</h2>

    <p>Once your grid formatted, it is fundamental to contribute it back to the QSOS community so others can use it to evaluate software from this family. Also future evolutions of the grid can then be managed in the QSOS repository.</p>

    <ol>
      <li>Post your grid on the qsos-general@nongnu.org mailing list.</li>
      <li>It will be added to the <a href="https://savannah.nongnu.org/cvs/?group=qsos">QSOS CVS</a> by a QSOS admin and the whole QSOS community will be able to access it.</li>
    </ol>

    <p>Document version : $Id: QSOS-HOWTO_functional_grid.php,v 1.2 2006/07/04 23:20:26 rsemeteys Exp $</p>

<?php foot()?>
