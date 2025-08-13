# Agent Management System Documentation

## Overview

The Agent Management System is a comprehensive solution for managing real estate agents and their sub-agents within the application. It provides a separate authentication system for agents with limited permissions compared to the admin dashboard.

## Features

### 🔐 Authentication System
- **Separate Login System**: Agents have their own login portal at `/agent/login`
- **Session Management**: Secure session handling with activity checks
- **Password Security**: Automatic password hashing and secure password generation
- **Account Status Validation**: Active/inactive account checks during authentication

### 👤 Agent Profile Management
- **Profile Viewing**: Agents can view their complete profile information
- **Profile Editing**: Update personal information, contact details, and qualifications
- **Password Changes**: Secure password update functionality
- **Unique Identifiers**: Auto-generated unique agent IDs and referral IDs

### 👥 Sub-Agent Management
- **Complete CRUD Operations**:
  - **Create**: Add new sub-agents with automatic credential generation
  - **Read**: View sub-agent lists with detailed information
  - **Update**: Edit sub-agent information and credentials
  - **Delete**: Remove sub-agents with confirmation dialogs

- **Advanced Features**:
  - **Pagination**: Server-side pagination for performance
  - **Search**: Search by name, email, phone, or qualification
  - **Filtering**: Filter by active/inactive status
  - **Sorting**: Sort by name, creation date (ascending/descending)

### 🔒 Security Features
- **Authorization**: Agents can only manage their own sub-agents
- **CSRF Protection**: All forms include CSRF token validation
- **Input Validation**: Comprehensive validation rules with user-friendly error messages
- **Parent-Agent Verification**: All sub-agent operations verify ownership

## File Structure

```
app/
├── Controllers/
│   └── AgentAuthController.php          # Main agent controller
├── Models/
│   └── AgentModel.php                   # Agent data model
├── Views/
│   └── agent/
│       └── dashboard/
│           ├── index.php                # Agent dashboard
│           ├── profile.php              # Profile management
│           ├── sub_agents.php           # Sub-agent listing
│           ├── create_sub_agent.php     # Create sub-agent form
│           ├── edit_sub_agent.php       # Edit sub-agent form
│           └── view_sub_agent.php       # Sub-agent details modal
├── Filters/
│   └── AgentAuthFilter.php              # Authentication filter
└── Commands/
    └── SetupTestAgent.php               # Test agent setup command
```

## Database Schema

### Agents Table
```sql
- id (Primary Key)
- referral_id (Unique, nullable)
- unique_agent_id (Unique, auto-generated)
- name (Required)
- email (Unique, required)
- password (Hashed, nullable)
- phone (Required)
- address (Optional)
- qualification (Optional)
- profile_image (Optional)
- parent_agent_id (Foreign Key, nullable)
- is_active (Boolean, default: true)
- created_at, updated_at (Timestamps)
```

## Routes

### Authentication Routes
```php
/agent/login          # GET/POST - Agent login
/agent/logout         # GET - Agent logout
```

### Protected Agent Routes (requires authentication)
```php
/agent/dashboard      # GET - Agent dashboard
/agent/profile        # GET/POST - Profile management
/agent/sub-agents     # GET - Sub-agent listing (with pagination/search)
/agent/sub-agents/create           # GET/POST - Create sub-agent
/agent/sub-agents/edit/{id}        # GET/POST - Edit sub-agent
/agent/sub-agents/view/{id}        # GET - View sub-agent (AJAX)
/agent/sub-agents/delete/{id}      # POST/DELETE - Delete sub-agent
```

## Usage Examples

### Setting Up Test Agent
```bash
php spark setup:testagent
```

### Agent Login Credentials (Test)
- **Email**: rajesh@realestate.com
- **Password**: agent123

### Creating Sub-Agents
1. Login as an agent
2. Navigate to "Sub-Agents" section
3. Click "Add New Sub-Agent"
4. Fill in the required information
5. System automatically generates:
   - Unique Agent ID
   - Referral ID
   - Secure password (sent via email)

### Search and Filter Sub-Agents
- **Search**: By name, email, phone, or qualification
- **Filter**: Active/Inactive status
- **Sort**: By name or creation date
- **Pagination**: 10 items per page

## Security Considerations

### Authorization Checks
Every sub-agent operation includes verification:
```php
$subAgent = $this->agentModel->where('id', $subAgentId)
                            ->where('parent_agent_id', $parentAgentId)
                            ->first();
```

### CSRF Protection
All forms include CSRF tokens:
```php
<?= csrf_field() ?>
```

### Input Validation
Comprehensive validation rules:
- Email uniqueness (excluding current record on updates)
- Password minimum length (6 characters)
- Required field validation
- Phone number formatting

## Error Handling

### User-Friendly Messages
- Success notifications for completed actions
- Detailed error messages for validation failures
- Confirmation dialogs for destructive actions
- Loading states for AJAX operations

### Logging
- Failed login attempts
- Sub-agent creation/update/deletion events
- Email sending failures
- Database operation errors

## Performance Optimizations

### Database Queries
- Indexed fields: unique_agent_id, referral_id, parent_agent_id
- Efficient pagination queries
- Optimized search with grouped conditions

### Frontend
- AJAX loading for sub-agent details
- Client-side form validation
- Responsive design for mobile devices

## Accessibility Features

- WCAG 2.1 AA compliance
- Keyboard navigation support
- Screen reader friendly markup
- High contrast color schemes
- Responsive design for all devices

## Future Enhancements

### Planned Features
- Email notification system for sub-agent credentials
- Advanced reporting and analytics
- Bulk operations for sub-agent management
- Integration with property assignment system
- Mobile app support

### Technical Improvements
- API endpoints for mobile applications
- Advanced caching mechanisms
- Real-time notifications
- Enhanced security features

## Troubleshooting

### Common Issues
1. **Agent can't login**: Check if account is active and password is correct
2. **Sub-agent not visible**: Verify parent-agent relationship
3. **Pagination not working**: Check database connection and query parameters
4. **CSRF errors**: Ensure forms include csrf_field() helper

### Debug Commands
```bash
# Check agent status
php spark db:query "SELECT * FROM agents WHERE email='agent@example.com'"

# Reset agent password
php spark setup:testagent

# Clear cache
php spark cache:clear
```

## Support

For technical support or feature requests, please contact the development team or create an issue in the project repository.

---

**Last Updated**: August 8, 2025  
**Version**: 2.0  
**Author**: Real Estate Development Team
