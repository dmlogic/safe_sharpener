
# SafeSharpener for Expression Engine 2

### The Problem

Safecracker is an excellent tool giving non-programmers a means to enable submission of channel entries outside of the CMS control panel.

You can show as many or as few of your channel fields as you like in your edit forms, those you don't include do not get updated. Conversely, those you
 do include *always* get updated, and this is a security problem.

For example, imagine an online application using a channel with many fields.
 Some are suitable for submission from a Safecracker form and some are not (perhaps 'admin notes' or 'store credits'. Could be anything).
 With SafeCracker in it's current state, if the name of a sensitive field can be established, it can be updated by injecting a
 hidden field into the edit form. This amounts to, at best, [Security through obscurity][1]
 and at worst a gaping security hole.

### The Solution

SafeSharpener provides a new template tag to specify which fields will be recognised by the submitted form on a global or per-member-group basis.

An extension then runs prior to SafeCracker processing that cleans the form submission of anything not included in your allowed fields.

### System requirements

EE2.13 and SafeCracker module installed

### Installing

Ensure that $config['encryption_key'] is set to something in your main config file. (sorry, forgot to include this step on release).

Place the folder&nbsp;*dm_safesharpener*&nbsp;in your&nbsp;*/system/expressionengine/third_party/*&nbsp;folder

Install the extension as normal via the EE control panel

Note: once installed, you MUST include the {exp:dm_safesharpener} template tag in all your SafeCracker forms or they will no longer work.

A single template tag is available and must be placed somewhere (anywhere) within each of your SafeCracker forms.

 [1]: http://en.wikipedia.org/wiki/Security_through_obscurity

#### Example


     {exp:safecracker
        channel="channel_name"
        other parameters...}

    {exp:dm_safesharpener
        allowed_fields="title|profile"
        allowed_fields_1="expiration_date|admin_notes|store_credits"}

     ... your form here ...

    {/exp:safecracker}

#### Parameters

##### allowed_fields=

`allowed_fields="field_1|field_2"`

A pipe-separated list of fieldnames that can be submitted by any valid member

##### allowed\_fields\_X=

`allowed_fields_X="field_3|field_4"`

A pipe-separated list of fieldnames that can be submitted *in addition to the above* by a particular member group. Replace X with the group_id of the member group.

##### disable=

`disable="yes"`

By default SafeSharpener runs on every SafeCracker form and any POSTed data not named by the above parameters will be removed. Use this tag
 to allow a form to be skipped by SafeSharpener

We hope that EllisLab will integrate the concepts of this Add-on into SafeCracker so no further development will be needed.