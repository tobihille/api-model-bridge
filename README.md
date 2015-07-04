# Goal

This module should enable everyone knowing magentos model-names to access them via soap-api.
The main reasons for creating this repo are:
- I'm tired of implementing dozends of api-calls for every new installed module
- Training myself

I'm aware that once granted access the api-user can literally do evererything, that's why there is a warning in System > Configuration > Magento Core API > Api-ModelBridge and why there is this additional config at all

# When to use this module

- If you are an Magento developer working with other developers which try to access data from outside of magento without connecting directly to the database
- If you have an api-user which is already admin (then this module can't bust your security)
- If you trust the developers using the api

# When *not* to use this module

- If you didn't uderstood one of the above points or if you're not met by "When to use this module"
- If you are even more concerned about security than me

# How to use

- Take a look at apiTest.php there is an example for every method.

##### If you really want to use this in production take care to not deploy apiTest.php scince with correct parameters it can delete at least some of your products or expose orders.

