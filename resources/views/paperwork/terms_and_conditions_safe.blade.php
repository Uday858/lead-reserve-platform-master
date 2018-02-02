<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
	    <!-- CSRF Token -->
	    <meta name="csrf-token" content="{{ csrf_token() }}">
	    <title>{{$publisher->name}} &mdash; Publisher Terms And Conditions, {{\Carbon\Carbon::today()->toFormattedDateString()}}</title>
	    <!-- Styles -->
	    <style>
	    	body {
	    		font-family:Arial;
	    	}
	    	table td {
	    		padding:10px;
	    		background-color:#ececec;
	    	}
	    	.col25 {
	    		display:table-cell;
	    		width:25%;
	    	}
	    	.col50 {
	    		display:table-cell;
	    		width:50%;
	    	}
	    	.legal-heading {
	    		text-decoration:underline;
	    		font-weight:900;
	    	}
	    </style>
	</head>
	<body>
			<h2 style="text-align:center"> Publisher Terms &amp; Conditions </h2>	

			<p>This Master Services Agreement (“MSA” or “Agreement”) is dated {{\Carbon\Carbon::today()->toFormattedDateString()}} (the “Effective Date”) and is between {{$publisher->name}} (“Publisher”) and Reserve Tech, Inc., a California corporation (“The Company”).</p>

			<ol>
				<li>
					<p class="legal-heading">General</p>
					<p>(a) From time to time, The Company may request that the Publisher provides certain services on a project-by-project basis (“Services”). The Services will be provided pursuant to separate and distinct terms agreed to by both parties (an “Insertion Order”) that incorporates certain terms of this agreement. This MSA shall be deemed incorporated by reference into any Insertion Order and shall govern all Insertion Orders, superseding all contrary terms set forth therein.</p>

					<p>(b) The Publisher shall provide the Services to The Company pursuant to each Insertion Order that is entered into during the term of this Agreement. Each Insertion Order will automatically incorporate the terms of this agreement and be a separate and distinct agreement. If there is a contradiction between a provision of this Agreement and an Insertion Order, then the provision in this agreement will take precedence unless the Insertion Order specifically states that it takes precedence over the provision.  An Insertion Order can consist of (i) a writing signed by both parties outlining the Services or (ii) an email from The Company requesting Services, provided the Publisher responds with a confirmatory email.  An Insertion Order will be effective as of the earlier of (i) execution of a written Insertion Order by both parties, (ii) the Publisher’s confirmatory email, or (iii) Publisher’s commencement of the Services.</p>
				</li>
				<li>
					<p class="legal-heading">Lead Definition</p>
					<p>(a) For the purpose of the Agreement, a valid, payable lead shall comprise of the following:  All fields and custom questions as specified in Insertion Order and according to the provided posting specifications, including a valid, unmanufactured date, time stamp, and ip address; all fields including but not limited to email address, telephone number and postal address (for campaigns where that data is required) must be valid.</p>

					<p>(b) A lead may not be the product of bots, automatic form fills, ‘data dumping,’ etc.; may not be coerced into registering for any reason other than the product offering of the campaign itself, ie. no prize points, cash incentives, free giveaway items, scholarships, airline miles, etc., and must be accepted as a valid lead by The Company’s client.</p>    
					
					<p>(c) Should The Company find that Publisher is violating the terms of this Agreement or in any way circumventing or attempting to circumvent the collection of valid leads, including but not limited to the following actions: engaging in any fraudulent activity; violating Section 2(b) above, violating  acceptable website content as outlined in Section 6 herein; having engaged in and/or engaging in any other practices that are not in the best interest of providing The Company’s or The Company’s client with legitimate, valid leads; failing to host a campaign according to The Company’s instructions; engaging in any practices that defame The Company or The Company’s client, The Publisher forfeits its rights to any outstanding amounts due for the specified campaign, in The Company’s sole discretion, and The Company shall have the right to terminate this Agreement with immediate effect.</p>
					
					<p> (d) Publisher specifically represents and warrants that it does not, directly or indirectly collect registration data through the use of unsolicited text messages, SPAM e-mails, or recorded voice messages to consumers to entice them to register on its website(s).  Further, at all times during the term hereof, Publisher covenants that it and all of its affiliates and representatives will not, directly or indirect, take any actions in connection with this Agreement in violation of any applicable law including, without limitation, the Telephone Consumer Protection Act.</p>
				</li>
				<li>
					<p class="legal-heading">Reporting And Confirmations</p>
					<p>(a) Reporting of leads will be made accessible to Publisher via The Company’s online reporting system.  Reports will reflect daily AND cumulative accepted leads for each individual campaign that Publisher is advertising for on the behalf of The Company.  <strong>These numbers will not reflect final, billable lead counts.</strong>  Month end, accepted lead counts must be approved in writing by a Company representative.  Publisher understands that The Company will put forth its best effort to finalize counts with the client by the 15th after the end of each month, but that the period for confirming numbers may change due to circumstances outside of The Company’s control.</p>
					
					<p>(b) Statements: It is the responsibility of The Publisher to send statements to The Company for all confirmed, billable lead counts as outlined in Section 2(a) and so that The Company can reconcile the billable lead counts provided by The Company’s client with the billable lead counts provided by The Publisher. The Company will not make any payment under Section 3(a) to The Publisher for any billable leads that are not included in a statement provided by The Publisher.  Statements must be sent to The Company within five (5) days after The Publisher receives confirmation of final billable numbers from The Company representative.</p>
				</li>
				<li>
					<p class="legal-heading">Payment And Invoicing</p>
					<p>(a) The Company will pay Publisher all agreed upon final billable leads or actions provided by Publisher on a cost per lead (“CPL”) or cost per action (“CPA”) basis in accordance with the terms of this Agreement and as outlined in Section 3(a). The Company shall remit such payment to the Publisher’s office on or before the 30th day following the date payment is received by The Company from The Company’s client for the specific campaign outlined in this Insertion Order, unless otherwise set forth on page one (1) of the Insertion Order(s).</p>
					<p>(b) <strong>Daily Caps, Overall Budgets, and Campaign End Dates</strong>: Publisher understands that The Company may implement daily caps (maximum number of deliverable leads to The Company by Publisher on a daily basis), overall lead volumes/budgets, and/or a campaign end date at the onset of the campaign as outlined in the Notes section of the corresponding Insertion Order.  Additionally, The Company may alter daily caps, overall budgets and/or campaign end dates during the course of the campaign, by providing The Publisher twenty-four (24) hour written or electronic notice.  The Publisher understands that The Company is not responsible for payment of leads in excess of daily caps and/or overall budgets, or for leads sent after the initial or revised campaign end date.</p>
					<h3>Send Invoices To:</h3>
					<table>
					<tr>
						<td style="text-align:center;width:380px;">Reserve Tech, Inc.</td>
					</tr>
					<tr>
						<td style="text-align:center;width:380px;">Attn: Accounts Payable</td>
					</tr>
					<tr>
						<td style="text-align:center;width:380px;">65 Enterprise</td>
					</tr>
					<tr>
						<td style="text-align:center;width:380px;">3rd Floor</td>
					</tr>
					<tr>
						<td style="text-align:center;width:380px;">Aliso Viejo</td>
					</tr>
					<tr>
						<td style="text-align:center;width:380px;">CA 92656</td>
					</tr>
				</table>
				</li>
				<li>
					<p class="legal-heading">Data Ownership</p>
					<p>Publisher understands that all data collected under this agreement is and shall be the sole property of The Company, and that The Company has exclusive right to use, sell or market the leads provided by The Publisher.  The Publisher assigns and shall assign to The Company all of Publisher’s right, title and interest in and to the leads collected under this Agreement, including individual data and aggregated data.  The Publisher may not contact the leads on behalf of The Company, The Company’s clients, or any other 3rd parties.   Publisher shall keep or maintain records of the data or required proof related to performance of this Agreement for a period of the greater of (a) required by law, or (b) three (3) years after termination of this Agreement. Company understands that the consumer associated with the data may select to sign up for additional campaigns on Publisher’s website and may also be sold to parties outside of this agreement.</p>
				</li>
				<li>
					<p class="legal-heading">Content</p>
					<p><strong>Reserve Tech, Inc. does not accept advertising on sites providing adult content, cursing or inappropriate language, sites promoting or exhibiting racism, sites promoting libel, sites that are under construction, hosted by a free service, personal home pages, or do not own the domain they are under, or any other sites that Company deems inappropriate in Company’s sole and absolute discretion.</strong></p>
				</li>
				<li>
					<p class="legal-heading">Non-Circumvention</p>
					<p>The Company has proprietary relationships with Lead Buyers.  The Publisher shall not circumvent or attempt to circumvent The Company with Lead Buyers that they provide Leads for to The Company.  With the exception of reasonably documented, preexisting relationships with these Lead Buyers, or relationships entered into in the ordinary course of Publisher’s businesses, during the term of this Master Services Agreement or for a period of 12 months, whichever is longer, Publisher agrees not to solicit, induce, recruit, or encourage, directly or indirectly, any Lead Buyer that Publisher knows, or has reason to know, has a proprietary relationship with The Company for the purpose of selling or obtaining Leads.  Publisher shall maintain confidentiality about business dealings, contractual terms, and proprietary information involved in any relationships between The Company, Publisher, and any Lead Buyers.  If Publisher breaches this obligation, Publisher shall be required to pay The Company in the amount of the revenue that Publisher obtains arising from or related to such breach, in addition to any other remedies available to The Company.</p>
				</li>
				<li>
					<p class="legal-heading">Termination</p>
					<p>(a) Either party may terminate this Agreement for any reason or for no reason, upon two (2) business days notice.  Should either party exercise its right to terminate this agreement, The Publisher will forfeit its right to payment for all leads delivered to The Company after expiration of the two (2) business day notice period in which written notice was supplied to The Publisher by The Company.</p>
					<p>(b) In the event this Agreement is terminated, all Insertion Orders will also automatically terminate as of the effective date of the termination of this Agreement.</p>
				</li>
				<li>
					<p class="legal-heading">Breach Of Contract, Liabilities, and Legal Venue</p>
					<p>(a) This Agreement shall be construed and interpreted pursuant to the laws of the State of California without consideration to its choice of law provisions.  The parties consent to the jurisdiction of the courts of the State of California, county of Los Angeles, and hereby waive all objections to such jurisdiction and venue.</p>
					<p>(b) In no event shall either party be liable for special, indirect, incidental, or consequential damages or loss of profits relating to the terms of this agreement.</p>
					<p>(c) Publisher agrees to pay all costs incurred by The Company as a result of The Publisher’s breach of the terms of this Agreement, including, but not limited to, The Company’s costs and fees associated with enforcing or interpreting its rights under this Agreement and The Company’s attorneys' fees.</p>
				</li>
				<li>
					<p class="legal-heading">Indemification</p>
					<p>(a) Publisher is solely responsible for all content and other material on its own website and its partners’ websites (where applicable) where the leads outlined in this agreement are originated. Publisher hereby agrees to indemnify, defend and hold harmless The Company, its officers, directors, agents, affiliates and employees from and against all claims, actions, liabilities, losses, expenses, damages, and costs (including, without limitation, reasonable attorneys’ fees) arising from or related to (i) The Publisher’s acts or omissions related to this Agreement including, without limitation, any violation of the terms and conditions of this Agreement, (ii) The Publisher’s websites; and/or (iii) The Publisher’s partners’ websites.</p>
					<p>(b) The Company hereby agrees to indemnify, defend and hold harmless Publisher from and against all claims, actions, liabilities, losses, expenses, damages and costs (including, without limitation, reasonable attorneys’ fees) arising from or related to The Company’s breach of this agreement.</p>
				</li>
				<li>
					<p class="legal-heading">General Provisions</p>

					<p>(a) The parties to this Agreement are independent contractors and no agency, partnership, joint venture or employer-employee relationship is intended or created hereby.</p>

					<p>(b) In the event of a conflict between the terms of this Agreement and the terms of any Insertion Order, the terms of this Agreement will govern, unless the Insertion Order specifically confirms that its terms, or a term, take precedence over this Agreement.</p>
					
					<p>(c) The parties agree that they will assure that they and their affiliates, subsidiaries, successors and assigns and their respective employees, agents, attorneys and representatives shall maintain as confidential, shall not use for the account of any entity other than the disclosing party, and shall not disclose or cause to be disclosed to any person or entity, any proprietary information of the other party to which such party may gain access as a result of its participation hereunder.  The foregoing shall not apply to information that: (i) Was previously known to the recipient free of any obligation to keep it confidential; (ii) Was independently developed by recipient; (ii) Is or becomes publicly available by means other than the unauthorized disclosure by recipient; or (iv) The recipient is required to disclose by any entity having appropriate jurisdiction.</p>
 					
 					<p>(d) This Agreement, including any accompanying Insertion Orders and/or attachments, sets forth the entire understanding and agreement of the parties and supersedes any and all prior oral or written agreements or understandings between the parties as to the subject matter of this Agreement and may be changed only by a subsequent writing signed by both parties.</p>
					
					<p>(e) This Agreement is non-exclusive to The Company and The Company shall have the right to enter into similar agreements with other third parties.</p>
					
					<p>(f) This Agreement is and shall be personal to The Publisher and shall not be assigned by any act of The Publisher or by operation of law.  Nothing herein shall prevent The Company from assigning its rights or obligations under the Agreement.</p>

					<p>(g) All notices and other communications which are required or which may be given under the provisions of this Agreement, unless otherwise specified, shall be in writing and may be delivered by personal service, may be mailed by registered or certified mail, postage prepaid to the parties, or may be sent via facsimile or e-mail during normal business hours, Monday through Friday.  When applicable, all notices and communications shall be deemed to have been received by the other party to whom it was addressed on the third (3rd) business day following the date of mailing.  All communications sent via facsimile or e-mail shall be deemed received upon the date of transmission if sent during the recipient’s normal business hours, or upon the next business day thereafter if not sent during the recipient’s normal business hours. Either party may change its address at any time by written notice to the other party as set forth above.</p>
					
					<p>(h) This Agreement shall be binding upon and shall inure to the benefit of all permitted successors and assigns of the parties.</p>
					
					<p>(i) No waiver by either party, whether expressed or implied, of any provision of this Agreement or of any breach or default of any party, shall constitute a continuing waiver of such provision or any other provisions of this Agreement, and no such waiver by any party shall prevent such party from acting upon the same or any subsequent default of any other part of any provisions of this Agreement.</p>
					
					<p>(j) If any provision of this Agreement, or part thereof, is declared invalid, void or otherwise unenforceable, such provision or part thereof shall be deemed severed from this Agreement and every other provision of this Agreement shall otherwise remain in full force and effect.  If any provision is held invalid as to duration, scope, activity, or subject, such provision shall be construed by limiting and reducing it so as to be enforceable to the extent compatible with applicable law.</p>
					
					<p>(k) This Agreement may be executed in one or more counterparts, each of which shall be deemed an original, but all of which together shall constitute one and the same document.</p>

					<p>(l) This Agreement shall become binding and enforceable upon a party at such time as a counterpart has been signed and either deposited in the mail, or transmitted via facsimile to the other party.  In the event of transmittal by facsimile, the signed counterpart shall be delivered within twenty-four (24) hours thereafter, but the failure to do so shall have no effect on the enforceability of this Agreement.</p>
					
					<p>(m) Each individual signing on behalf of a party hereto represents and warrants that he/she is duly authorized by such party to execute this Agreement on behalf of such party.</p>
					
					<p>(n) All terms, conditions, obligations and provisions capable of surviving the termination or expiration of this Agreement shall so survive.</p>
				</li>
			</ol>

			<h3>The parties execute this Agreement on the terms and conditions set forth herein.
</h3>
				<h2>Reserve Tech, Inc.</h2>
				<table>
					<tr>
						<td>Signature:</td>
						<td></td>
					</tr>
					<tr>
						<td>Name:</td>
						<td>Thomas Cutting</td>
					</tr>
					<tr>
						<td>Title:</td>
						<td>President</td>
					</tr>
					<tr>
						<td>Date:</td>
						<td>{{\Carbon\Carbon::today()->toFormattedDateString()}}</td>
					</tr>
				</table>	
				<h2>{{$publisher->name}}</h2>
				<table>
					<tr>
						<td>Signature:</td>
						<td></td>
					</tr>
					<tr>
						<td>Name:</td>
						<td></td>
					</tr>
					<tr>
						<td>Title:</td>
						<td></td>
					</tr>
					<tr>
						<td>Date:</td>
						<td></td>
					</tr>
				</table>
	</body>
</html>