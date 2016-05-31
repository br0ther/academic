<?php

namespace BugTrackBundle\Security;


final class Credential
{
    /* User */
    const EDIT_PROFILE = 'edit_profile';
    const EDIT_ROLES = 'edit_roles';
    
    /* Project */
    const VIEW_PROJECT = 'view_project';
    const EDIT_PROJECT = 'edit_project';
    const CREATE_PROJECT = 'create_project';
    
    /* Issue */    
    const CREATE_ISSUE = 'create_issue';
}
