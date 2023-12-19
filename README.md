   <h1>Mini Doc</h1>
   <p>url api: <a href="https://partiel.dlfcaroline.online">https://partiel.dlfcaroline.online</a></p>

<ul>
   <h2>User and Token</h2>
   <li><strong><u>GET</u> /register </strong>: Sign up
      <ul>
         <li>Body: username, email, password</li>
      </ul>
   </li>
   <li><strong><u>POST</u> /api/login_check </strong>: Login and get a token
      <ul>
         <li>Body: username, password</li>
      </ul>
   </li>
   <li><strong><u>POST</u> /api/refresh/token </strong>: Refresh a token
      <ul>
         <li>Body: refresh_token</li>
      </ul>
   </li>
</ul>

<ul>
   <h2>Events</h2>
   <li><strong><u>POST</u> /api/event/new </strong>: Create an event
      <ul>
         <li>Body: description, location, location public (boolean), public (boolean), firstDay (ex: 11.02.2012), lastDay</li>
      </ul>
   </li>
   <li><strong><u>GET</u> /api/events </strong>: Index all events</li>
   <li><strong><u>GET</u> /api/event/{eventId} </strong>: Index one event</li>
   <li><strong><u>GET</u> /api/event/{eventId}/participants </strong>: Index all participants of an event</li>
   <li><strong><u>POST</u> /api/event/{eventId}/attend </strong>: Attend a public event</li>
   <li><strong><u>PUT</u> /api/event/{eventId}/edit/days </strong>: Modify the dates of an event
      <ul>
         <li>Body: firstDay (ex: 11.02.2012), lastDay</li>
      </ul>
   </li>
   <h3>Specific to private events</h3>
   <li><strong><u>GET</u> /api/event/{eventId}/invitations </strong>: Index all invited people to a private event</li>
   <li><strong><u>POST</u> /api/event/{eventId}/contribution/add </strong>: Add a contribution to the event
      <ul>
         <li>Body: name</li>
      </ul>
   </li>
   <li><strong><u>GET</u> /api/private/event/{eventId}/contributions </strong>: Index all made contributions</li>
   <li><strong><u>DELETE</u> /api/private/event/contribution/{contributionId}/remove </strong>: Delete a contribution</li>
   <li><strong><u>GET</u> /api/private/event/{eventId}/suggestions </strong>: Index all made suggestions</li>
   <li><strong><u>POST</u> /api/private/event/{eventId}/suggestion/new </strong>: Add a suggestion to the event
      <ul>
         <li>Body: name</li>
      </ul>
   </li>
   <li><strong><u>POST</u> /api/private/event/suggestion/{suggestionId}/handle </strong>: Take care of a suggestion</li>
   <li><strong><u>PUT</u> /api/private/event/suggestion/{suggestionId}/modify </strong>: Modify a suggestion
      <ul>
         <li>Body: name</li>
      </ul>
   </li>
   <li><strong><u>GET</u> /api/private/event/{eventId}/invitations </strong>: Index all invited people</li>


</ul>

<ul>
   <h2>Profiles</h2>
   <li><strong><u>GET</u> /api/profiles</strong>: Index all profiles</li>
   <li><strong><u>GET</u> /api/profile/{profileId}/invitations </strong>: Index all received invitations</li>
   <li><strong><u>GET</u> /api/events/profile/{profileId} </strong>: Index all events you participate in</li>
</ul>

<ul>
   <h2>Invitations</h2>
   <li><strong><u>POST</u> /api/event/{eventId}/invite </strong>: Invite people to your private event
      <ul>
         <li>Body: invitations (array of profile Ids)</li>
      </ul>
   </li>
   <li><strong><u>POST</u> /api/invite/{inviteId}/accept</strong>: Accept an invitation</li>
   <li><strong><u>POST</u> /api/invite/{inviteId}/refuse</strong>: Refuse an invitation</li>
</ul>
