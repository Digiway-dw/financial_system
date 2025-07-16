# Agent Permissions Guide

## Overview

This document outlines the permissions and capabilities of agents in the financial system, with a specific focus on customer management and transaction operations.

## Agent Capabilities

### Customer Management
Agents have full CRUD capabilities for customers (except deletion):

1. **View Customers**: Agents can view the customer list and customer details
2. **Create Customers**: Agents can register new customers in the system
3. **Update Customers**: Agents can edit and update customer information
4. **Search Customers**: Agents can search for customers using filters

Note: Only Admin, General Supervisor, and Branch Managers can delete customers to prevent fraud.

### Transaction Management
Agents can create and manage transactions with specific limitations:

1. **Create Transactions**:
   - Send money transactions
   - Receive money transactions
   - Cash transactions (deposits/withdrawals)

2. **View Transactions**:
   - Agents can ONLY see transactions they personally created
   - Transactions from other agents are not visible

3. **Safe Transfers**:
   - Agents can initiate safe transfers
   - But cannot approve safe transfers (requires manager approval)

## Access Restrictions

Agents are restricted from accessing:

1. **User Management**:
   - Cannot access the user list
   - Cannot create/edit/delete other users
   - Can only view their own profile

2. **Financial Reports**:
   - Cannot access company-wide financial reports
   - Cannot view profit reports or detailed financial data

3. **System Management**:
   - Cannot manage safes or modify balances
   - Cannot manage branches or system settings

## Authorization Implementation

The agent permissions are enforced through multiple layers:

1. **Gates**:
   - `view-customers`, `create-customers`, `edit-customers` gates allow agents
   - `delete-customers` gate excludes agents
   - `view-agent-dashboard` identifies agent role

2. **Policies**:
   - `CustomerPolicy` enforces customer management permissions
   - `AgentPolicy` enforces transaction visibility (own transactions only)

3. **Middleware**:
   - `AgentDashboardAccess` middleware restricts agents from accessing unauthorized areas

## Security Notes

1. Transaction isolation ensures agents can only see their own transactions
2. The system logs all agent activities for audit purposes
3. Agents can never delete customers to prevent fraudulent removal of customer records
4. Transactions created by agents may require approval based on amount or risk level
