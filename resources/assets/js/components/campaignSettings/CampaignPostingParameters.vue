<template>
    <div class="panel panel-default">
        <div class="panel-heading blue">Campaign Posting Parameters</div>
        <div class="panel-body">
            <p class="text-center">
                <i class="fa fa-cog fa-spin fa-3x fa-fw" v-if="currentlyLoading"></i>
            </p>
            <div class="campaignPostingParameterTable" v-if="!currentlyLoading">
                <table class="table table-hover">
                    <tr>
                        <th>Type</th>
                        <th>Label</th>
                        <th>Publisher Field</th>
                        <th>Advertiser Field</th>
                        <th>Value(s)</th> <!-- Hardcoded Value or Textarea's for inclusion -->
                        <th>Specification Caption</th> <!-- Posting Specification Box -->
                        <th>TF Value</th>
                        <th></th>
                    </tr>
                    <tr v-for="(param,key) in params">
                        <td>
                            <select class="form-control" v-model="param.type">
                                <option disabled value="">Please Select One</option>
                                <option value="field">Field Variable</option>
                                <option value="hardcoded">Static/Hardcoded Variable</option>
                                <option value="system">System Variable</option>
                                <option value="inclusion">Inclusion (List) Variable</option>
                                <option value="random">Random Variable</option>
                            </select>
                        </td>
                        <td>
                            <input class="form-control" v-model="param.label"
                                   placeholder="Readable Label"/>
                        </td>
                        <td>
                            <input class="form-control" v-model="param.incoming_field" 
                                   v-bind:disabled="param.type === 'hardcoded' || param.type === 'system' || param.type === 'random'"
                                   placeholder="Publisher (Incoming) Field"/>
                        </td>
                        <td>
                            <input class="form-control" v-model="param.outgoing_field" 
                                   placeholder="Advertiser (Outgoing) Field"/>
                        </td>
                        <td>
                            <div v-if="param.type === 'field'">
                                <p>
                                    <small>
                                        Not Applicable
                                    </small>
                                </p>
                            </div>
                            <div v-if="param.type === 'hardcoded'">
                                <div class="form-group full-width">
                                    <p>
                                        <small>
                                            Invisible
                                        </small>
                                    </p>
                                    <input class="form-control" v-model="param.hardcoded_value"
                                           placeholder="Hardcoded Value"/>
                                </div>
                            </div>
                            <div v-if="param.type === 'system'">
                                <div class="form-group full-width">
                                    <select class="form-control" v-model="param.system_value">
                                        <option disabled value="">Please Select One</option>
                                        <option value="campaign_id">Campaign ID</option>
                                        <option value="publisher_id">Publisher ID</option>
                                        <option value="timestamp">Timestamp</option>
                                        <option value="ip">IP Address</option>
                                        <option value="ua">User Agent</option>
                                        <option value="tf_cert">Trusted Form Certificate</option>
                                    </select>
                                </div>
                            </div>
                            <div v-if="param.type === 'inclusion'">
                                <div class="form-group full-width">
                                    <p>
                                        <small>
                                            Place your comma-separated values inside of the text box.
                                        </small>
                                    </p>
                                <textarea class="form-control" v-model="param.inclusion_value" rows="1"
                                          placeholder="Value 1, Value 2, Value 3"></textarea>
                                </div>
                            </div>
                            <div v-if="param.type === 'random'">
                                <div class="form-group full-width">
                                    <p>
                                        <small>
                                            Place your comma-separated values inside of the text box. Our system will
                                            automatically choose a singular value.
                                        </small>
                                    </p>
                                <textarea class="form-control" v-model="param.random_value" rows="1"
                                          placeholder="Value 1, Value 2, Value 3"></textarea>
                                </div>
                            </div>
                        </td>
                        <td>
                        <textarea class="form-control" v-model="param.spec_caption"
                                  v-bind:disabled="param.type === 'hardcoded' || param.type === 'system' || param.type === 'random'"
                                  rows="1">{{param.specCaption}}</textarea>
                        </td>
                        <td>
                            <select class="form-control" v-model="param.tf_value">
                                <option disabled value="">Please select one</option>
                                <option value="first_name">First Name</option>
                                <option value="last_name">Last Name</option>
                                <option value="email_address">Email Address</option>
                                <option value="address1">Address #1</option>
                                <option value="address2">Address #2</option>
                                <option value="city">City</option>
                                <option value="state">State</option>
                                <option value="zipcode">Zip</option>
                                <option value="phonenumber">Phone</option>
                                <option value="dob">Date Of Birth</option>
                            </select>
                        </td>
                        <td>
                            <a href="#" v-on:click="removeField(key)">
                                <i class="fa fa-close"></i>
                            </a>
                        </td>
                    </tr>
                </table>
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-success" type="button" v-on:click="addNewField()">
                            Add New Field
                        </button>
                        <button class="btn btn-primary" type="button" v-on:click="saveChanges()">
                            Save Changes <i class="fa fa-cog fa-spin fa-fw" v-if="currentlySaving"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import {ResourceHttp} from '../../services/Resource';
    import ParamFormElement from './PostingParameterFormElement.vue';
    export default {
        name: "CampaignPostingParameters",
        components: {
            "param-form-element": ParamFormElement
        },
        props: ["campaignid"],
        data() {
            return {
                params: [],
                currentlyLoading: true,
                currentlySaving: false
            };
        },
        mounted() {
            ResourceHttp.get("com/campaign-fields/" + this.campaignid).then((res) => {
                this.currentlyLoading = false;
                let data = res.data;
                this.params = (data === "default") ? this._defaultFields() : data;
            });
        },
        methods: {
            /**
             * Add a new field to the params data field.
             */
            addNewField: function () {
                this.params.push({
                    type: "",
                    label: "",
                    incoming_field: "",
                    outgoing_field: "",
                    hardcoded_value: "",
                    system_value: "",
                    inclusion_value: "",
                    random_value: "",
                    spec_caption: ""
                });
            },
            /**
             * Remove a certain field from the params data field.
             */
            removeField: function (index) {
                this.params.splice(index, 1);
            },
            /**
             * Save changes to the campaignFields.
             */
            saveChanges: function () {
                this.currentlySaving = true;
                ResourceHttp.post("com/campaign-fields/" + this.campaignid, this.params).then((res) => {
                    this.currentlySaving = false;
                    let data = res.data;
                });
            },
            /**
             * Retrieve default fields for new fields.
             * @returns {*[]}
             * @private
             */
            _defaultFields () {
                return [
                    {
                        type: "field",
                        label: "First Name",
                        incoming_field: "first-name",
                        outgoing_field: "",
                        hardcoded_value: "",
                        system_value: "",
                        inclusion_value: "",
                        random_value: "",
                        spec_caption: ""
                    },
                    {
                        type: "field",
                        label: "Last Name",
                        incoming_field: "last-name",
                        outgoing_field: "",
                        hardcoded_value: "",
                        system_value: "",
                        inclusion_value: "",
                        random_value: "",
                        spec_caption: ""
                    },
                    {
                        type: "field",
                        label: "Email Address",
                        incoming_field: "email-address",
                        outgoing_field: "",
                        hardcoded_value: "",
                        system_value: "",
                        inclusion_value: "",
                        random_value: "",
                        spec_caption: ""
                    },
                    {
                        type: "field",
                        label: "Phone Number",
                        incoming_field: "phone-number",
                        outgoing_field: "",
                        hardcoded_value: "",
                        system_value: "",
                        inclusion_value: "",
                        random_value: "",
                        spec_caption: ""
                    },
                    {
                        type: "field",
                        label: "Address Line 1",
                        incoming_field: "address-1",
                        outgoing_field: "",
                        hardcoded_value: "",
                        system_value: "",
                        inclusion_value: "",
                        random_value: "",
                        spec_caption: ""
                    },
                    {
                        type: "field",
                        label: "Address Line 2",
                        incoming_field: "address-2",
                        outgoing_field: "",
                        hardcoded_value: "",
                        system_value: "",
                        inclusion_value: "",
                        random_value: "",
                        spec_caption: ""
                    },
                    {
                        type: "field",
                        label: "City",
                        incoming_field: "city",
                        outgoing_field: "",
                        hardcoded_value: "",
                        system_value: "",
                        inclusion_value: "",
                        random_value: "",
                        spec_caption: ""
                    },
                    {
                        type: "field",
                        label: "State",
                        incoming_field: "state",
                        outgoing_field: "",
                        hardcoded_value: "",
                        system_value: "",
                        inclusion_value: "",
                        random_value: "",
                        spec_caption: ""
                    },
                    {
                        type: "field",
                        label: "ZIP Code",
                        incoming_field: "zipcode",
                        outgoing_field: "",
                        hardcoded_value: "",
                        system_value: "",
                        inclusion_value: "",
                        random_value: "",
                        spec_caption: ""
                    }
                ];
            }
        }
    }
</script>