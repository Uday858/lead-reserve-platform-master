<template>
    <div class="PostingParamEditableComponent">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-3">
                        <form>
                            <div class="form-group">
                                <div class="input-group">
                                    <select class="form-control" v-model="defaultFieldSelector">
                                        <option disabled value="">Please Select One</option>
                                        <option v-for="field in defaultFieldNames" v-bind:value="field.value">{{field.name}}
                                            ({{field.value}})
                                        </option>
                                        <option value="custom">Custom Incoming Field</option>
                                    </select>
                                    <p>
                                        <small>
                                            Choose a pre-defined or custom field to start with.
                                        </small>
                                    </p>
                                </div>
                                <div class="input-group" v-if="isCustomIncoming">
                                    <input class="form-control" v-model="fields.customIncoming"
                                           placeholder="some_kindOf+Field"/>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-9" v-if="defaultFieldSelector!=''">
                        <div class="form-group">
                            <input class="form-control" v-model="fields.outgoing" placeholder="Advertiser Field"/>
                        </div>
                        <div class="form-group">
                            <select class="form-control" v-model="typeSelector">
                                <option disabled value="">Please Select One</option>
                                <option value="field">Field Variable</option>
                                <option value="hardcoded">Static/Hardcoded Variable</option>
                                <option value="system">System Variable</option>
                                <option value="inclusion">Inclusion (List) Variable</option>
                                <option value="random">Random Variable</option>
                            </select>
                        </div>
                        <form class="form-inline">
                            <div v-if="typeSelector === 'field'">
                                <div class="form-group full-width">
                                    <p>
                                        <small>
                                            This text box controls what publishers see within the posting specifications sheet.
                                        </small>
                                    </p>
                                    <textarea class="form-control" rows="4" placeholder="Example of usage..."></textarea>
                                </div>
                            </div>
                            <div v-if="typeSelector === 'hardcoded'">
                                <div class="form-group full-width">
                                    <p>
                                        <small>
                                            No publisher is able to see these variables. This is all from a back-end side.
                                        </small>
                                    </p>
                                    <input class="form-control" placeholder="Hardcoded Value"/>
                                </div>
                            </div>
                            <div v-if="typeSelector === 'system'">
                                <div class="form-group full-width">
                                    <select class="form-control">
                                        <option disabled value="">Please Select One</option>
                                        <option value="campaign_id">Campaign ID</option>
                                        <option value="publisher_id">Publisher ID</option>
                                        <option value="timestamp">Timestamp</option>
                                        <option value="ip">IP Address</option>
                                        <option value="ua">User Agent</option>
                                    </select>
                                </div>
                            </div>
                            <div v-if="typeSelector === 'inclusion'">
                                <div class="form-group full-width">
                                    <p>
                                        <small>
                                            Place your comma-separated values inside of the text box.
                                        </small>
                                    </p>
                                    <p>
                                        <small>
                                            <strong>ZIP Code Example</strong><br/>
                                            92656,90001,32677
                                        </small>
                                    </p>
                                    <textarea class="form-control" rows="4" placeholder="Value 1, Value 2, Value 3"></textarea>
                                    <p>
                                        <small>
                                            A posting specification explainer text box.
                                        </small>
                                    </p>
                                    <textarea class="form-control" rows="4" placeholder="Example of value1, value2, value3..."></textarea>
                                </div>
                            </div>
                            <div v-if="typeSelector === 'random'">
                                <div class="form-group full-width">
                                    <p>
                                        <small>
                                            Place your comma-separated values inside of the text box. Our system will automatically choose a singular value.
                                        </small>
                                    </p>
                                    <textarea class="form-control" rows="4" placeholder="Value 1, Value 2, Value 3"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    export default {
        data: function() {
            return {
                isCustomIncoming: false,
                defaultFieldSelector: "",
                typeSelector: "",
                fields:{
                    customIncoming: "",
                    outgoing: ""
                },
                customIncomingField: "",
                defaultFieldNames: [
                    {
                        name: "First Name",
                        value: "first-name"
                    },
                    {
                        name: "Last Name",
                        value: "last-name"
                    },
                    {
                        name: "Email Address",
                        value: "email-address"
                    },
                    {
                        name: "Phone Number",
                        value: "phone-number"
                    },
                    {
                        name: "Address Line 1",
                        value: "address-1"
                    },
                    {
                        name: "Address Line 2",
                        value: "address-2"
                    },
                    {
                        name: "City",
                        value: "city"
                    },
                    {
                        name: "State",
                        value: "state"
                    },
                    {
                        name: "ZIP Code",
                        value: "zipcode"
                    }
                ]
            }
        },
        watch: {
            defaultFieldSelector: function(val) {
                this.isCustomIncoming = (val === "custom");
            }
        }
    }
</script>