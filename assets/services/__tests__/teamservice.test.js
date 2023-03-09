
import {teamservice} from "../teamservice";

import axios from "axios";
import MockAdapter from "axios-mock-adapter";
import { describe, it, expect, beforeAll, afterEach } from 'vitest'

// This sets the mock adapter on the default instance
var mock = new MockAdapter(axios);

describe("testservice", () => {
    let mock;

    beforeAll(() => {
        mock = new MockAdapter(axios);
        axios.defaults.validateStatus = function () {
            return true;
        };
    });

    afterEach(() => {
        mock.reset();
    });

    describe("Send invite", () => {
        it("Should return response if successful", async () => {

            const id = "an-id-here";
            const email = "iain.cambridge@example.org";

            mock.onPost("/api/user/team/invite", {email}).reply(200, {success: true});

            // when
            const result = await teamservice.invite(email)

            // then
            expect(mock.history.post[0].url).toEqual("/api/user/team/invite");
            expect(result.data).toEqual({success: true});
        });

        it("Should return error if hit limit", async () => {

            const id = "an-id-here";
            const email = "iain.cambridge@example.org";

            mock.onPost("/api/user/team/invite", {email}).reply(200, {success: false, hit_limit: true});

            try {
                await teamservice.invite(email);
                fail("Didn't throw error");
            } catch (error) {
                expect(mock.history.post[0].url).toEqual("/api/user/team/invite");
                expect(error).toEqual("No more invites available");

            }
        });

        it("Should return error if false", async () => {

            const id = "an-id-here";
            const email = "iain.cambridge@example.org";

            mock.onPost("/api/user/team/invite", {email}).reply(200, {success: false, hit_limit: false, already_invited: false});

            try {
                await teamservice.invite(email);
                fail("Didn't throw error");
            } catch (error) {
                expect(mock.history.post[0].url).toEqual("/api/user/team/invite");
                expect(error).toEqual("There was an unexpected error. Please try later.");
            }
        });

        it("Should return error if already invited", async () => {

            const id = "an-id-here";
            const email = "iain.cambridge@example.org";

            mock.onPost("/api/user/team/invite", {email}).reply(200, {success: false, hit_limit: false, already_invited: true});

            try {
                await teamservice.invite(email);
                fail("Didn't throw error");
            } catch (error) {
                expect(mock.history.post[0].url).toEqual("/api/user/team/invite");
                expect(error).toEqual("User already invited");
            }
        });

        it("Should return error", async () => {

            const id = "an-id-here";
            const email = "iain.cambridge@example.org";

            mock.onPost("/api/user/team/invite", {email}).reply(400, {success: false, error: "Invalid code"});

            try {
                await teamservice.invite(email);
                fail("Didn't throw error");
            } catch (error) {
                expect(mock.history.post[0].url).toEqual("/api/user/team/invite");
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("Cancel Invite", () => {
        it("Should return response if successful", async () => {

            const id = "an-id-here";
            const invite = {id}

            mock.onPost("/api/user/team/invite/"+invite.id+"/cancel").reply(200, {success: true});

            // when
            const result = await teamservice.cancelInvite(invite)

            // then
            expect(mock.history.post[0].url).toEqual("/api/user/team/invite/"+invite.id+"/cancel");
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            const id = "an-id-here";
            const invite = {id}

            mock.onPost("/api/user/team/invite/"+invite.id+"/cancel").reply(400, {success: false, error: "Invalid code"});

            try {
                await teamservice.cancelInvite(invite);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual("/api/user/team/invite/"+invite.id+"/cancel");
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("Disable Member", () => {
        it("Should return response if successful", async () => {

            const id = "an-id-here";
            const member = {id}

            mock.onPost("/api/user/team/member/"+member.id+"/disable").reply(200, {success: true});

            // when
            const result = await teamservice.disableMember(member)

            // then
            expect(mock.history.post[0].url).toEqual("/api/user/team/member/"+member.id+"/disable");
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            const id = "an-id-here";
            const member = {id}

            mock.onPost("/api/user/team/member/"+member.id+"/disable").reply(400, {success: false, error: "Invalid code"});

            try {
                await teamservice.disableMember(member);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual("/api/user/team/member/"+member.id+"/disable");
                expect(error).toEqual("Invalid code");

            }
        });
    });

})