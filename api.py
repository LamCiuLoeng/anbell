# -*- coding: utf-8 -*-
import urllib, urllib2, json, codecs, random

def ecsape_bom( s ):
    return s[3:] if s[:3] == codecs.BOM_UTF8 else s


def get_result( url, data ):
    result = urllib2.urlopen( url, urllib.urlencode( data ) )
    print '%s?%s' % ( url, urllib.urlencode( data ) )
    k = ecsape_bom( ''.join( result.readlines() ) )
    return json.loads( k )


def test():
    #===========================================================================
    # login to the server
    #===========================================================================
    login_url = 'http://localhost/index.php/API/v1/login'
    action_url = 'http://localhost/index.php/API/v1/action'


    param = {
             'system_no' : '10000002' , 'password' : '102',
             }

    user = get_result( login_url, param )
    assert user['flag'] == 0
    assert 'user' in user
    assert user['user_key'] is not None
    print '---pass login'

    user_key = user['user_key']

    #===========================================================================
    # get user info
    #===========================================================================
    param = {
             '_q' : 'get_user_info',
             'user_key' : user_key,
             'user_id'  : user['user']['id'],
             }
    info = get_result( action_url, param )
    assert info['flag'] == 0
    assert 'data' in info
    print '---pass get_user_info'

    school_id, grade, class_id, plan_id = \
    map( unicode, [info['data']['school_id'], info['data']['grade'], info['data']['class_id'], info['data']['plan_id']] )


    #===========================================================================
    # get user's plan
    #===========================================================================
    param = {
             '_q' : 'get_course_by_user',
             'user_key' : user_key,
             'user_id'  : user['user']['id'],
             }


    course_id = None
    courseware_id = None
    course = get_result( action_url, param )
    assert course['flag'] == 0
    assert 'data' in course
    assert type( course['data'] ) == dict
    if len( course['data'].keys() ) > 0:
        r = course['data'][course['data'].keys()[0]]
        assert 'name' in r
        assert 'coursewares' in r
        assert type( r['coursewares'] ) == list
        course_id = course['data'].keys()[0]
        if len( r['coursewares'] ) > 0:
            assert 'id' in r['coursewares'][0]
            assert 'name' in r['coursewares'][0]
            assert 'url' in r['coursewares'][0]

            courseware_id = r['coursewares'][0]['id']

    print '---pass get_course_by_user'



    if course_id:
        param = {
                 '_q' : 'get_course_info',
                 'user_key' : user_key,
                 'course_id'  : course_id,
                 }
        course = get_result( action_url, param )


        print '---------'
        print param

        assert course['flag'] == 0
        assert 'id' in course['data']
        assert 'name' in course['data']
        assert 'coursewares' in course['data']
        assert type( course['data']['coursewares'] ) == list
        if len( course['data']['coursewares'] ) > 0:
            assert 'id' in course['data']['coursewares'][0]
            assert 'name' in course['data']['coursewares'][0]
            assert 'url' in course['data']['coursewares'][0]
        print '---pass get_course_info c'

    #===========================================================================
    # save user's data
    #===========================================================================

    param1 = {
             '_q' : 'save_user_data',
             'user_key' : user_key,
             'user_id'  : user['user']['id'],
             'data_type' : 'P',
             'obj_id' : 1,
             'begin_or_end' : 'BEGIN',
             'school_id' : school_id,
             'class_id' : class_id,
             'grade' : grade,
             'plan_id' : plan_id,
             }
    save1 = get_result( action_url, param1 )
    assert save1['flag'] == 0
    print '---pass save_user_data c'

    param2 = {
             '_q' : 'save_user_data',
             'user_key' : user_key,
             'user_id'  : user['user']['id'],
             'data_type' : 'P',
             'obj_id' : 1,
             'score'  : random.randint( 10, 100 ),
             'begin_or_end' : 'END',
             'school_id' : school_id,
             'class_id' : class_id,
             'grade' : grade,
             'plan_id' : plan_id,
             }
    save1 = get_result( action_url, param2 )
    assert save1['flag'] == 0
    print '---pass save_user_data g'


    if course_id:
        param3 = {
                 '_q' : 'get_questions',
                 'course_id' : course_id,
                 'user_key' : user_key,
                 }
        qs = get_result( action_url, param3 )
        assert qs['flag'] == 0
        assert 'data' in qs
        assert type( qs['data'] ) == list
        if( len( qs['data'] ) > 0 ):
            assert 'content' in  qs['data'][0]
            assert 'correct_answer' in  qs['data'][0]

        print '---pass get_questions g'

    param4 = {
             '_q' : 'get_study_log',
             'user_id' : user['user']['id'],
             'user_key' : user_key,
             }
    qs = get_result( action_url, param4 )
    assert qs['flag'] == 0
    assert 'data' in qs
    assert type( qs['data'] ) == list
    if( len( qs['data'] ) > 0 ):
        assert qs['data'][0]['id']
    print '---pass get_study_log g'

    print 'finished'

if __name__ == "__main__":
    test()
