# -*- coding: utf-8 -*-
'''
###########################################
#  Created on 2014-7-13
#  @author: cl.lam <lamciuloeng@gmail.com>
#  Description:
###########################################
'''
from datetime import datetime as dt
from sqlalchemy import create_engine, Column, ForeignKey, Table
from sqlalchemy.types import Integer, DateTime, Text, Unicode, Float
from sqlalchemy.orm import scoped_session, sessionmaker, relation, backref, synonym
from sqlalchemy.ext.declarative import declarative_base


SQLALCHEMY_DATABASE_URI = "mysql://root:root@127.0.0.1:3306/anbels?charset=utf8"

engine = create_engine( SQLALCHEMY_DATABASE_URI, echo = False, pool_size = 20 )
maker = sessionmaker( bind = engine, autoflush = True, autocommit = False )
DBSession = scoped_session( maker )
DeclarativeBase = declarative_base()
metadata = DeclarativeBase.metadata

# DBSession.configure(bind = engine)

#===============================================================================
# model define
#===============================================================================

class SysMixin( object ):
    create_time = Column( DateTime, default = dt.now )
    create_by_id = Column( Integer, default = 1 )
    update_time = Column( DateTime, default = dt.now, onupdate = dt.now )
    update_by_id = Column( Integer, default = 1 )
    active = Column( Integer, default = 0 )  # 0 is active ,1 is inactive


#===============================================================================
# auth
#===============================================================================
user_group_table = Table( 'anbels_auth_group_access', metadata,
    Column( 'uid', Integer, ForeignKey( 'anbels_auth_user.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'group_id', Integer, ForeignKey( 'anbels_auth_group.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True )
 )


class User( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_auth_user'

    id = Column( Integer, autoincrement = True, primary_key = True )
    system_no = Column( Unicode( 20 ), nullable = False )
    name = Column( Unicode( 1000 ) , nullable = False )
    password = Column( Unicode( 1000 ), nullable = False )
    gender = Column( Unicode( 10 ), )
    last_login_time = Column( DateTime )
    salt = Column( Text )



class Group( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_auth_group'

    id = Column( Integer, autoincrement = True, primary_key = True )
    title = Column( Unicode( 1000 ), nullable = False )
    display_name = Column( Unicode( 1000 ) )
    status = Column( Integer, default = 1 )
    rules = Column( Unicode( 1000 ) )
    users = relation( 'User', secondary = user_group_table, backref = 'groups' )


class Rule( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_auth_rule'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    title = Column( Unicode( 1000 ) )
    type = Column( Integer, default = 1 )
    status = Column( Integer, default = 1 )
    condition = Column( Unicode( 1000 ) )

#===============================================================================
# master
#===============================================================================
class Location( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_location'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ) )
    code = Column( Unicode( 1000 ) )
    parent_code = Column( Unicode( 1000 ) )
    full_name = Column( Text )
    full_path_ids = Column( Text )
#     parent_id = Column( Integer, default = None )

    def __str__( self ): return self.full_name
    def __repr__( self ): return self.full_name
    def __unicode__( self ): return self.full_name


class Course( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_course'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    desc = Column( Text )


class Question( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_question'

    id = Column( Integer, autoincrement = True, primary_key = True )
    course_id = Column( Integer, ForeignKey( 'anbels_master_course.id' ) )
    course = relation( Course )
    content = Column( Text )
    correct_answer = Column( Unicode( 10 ), )
    answer01 = Column( Unicode( 10 ), )
    answer01_content = Column( Text )
    answer02 = Column( Unicode( 10 ), )
    answer02_content = Column( Text )
    answer03 = Column( Unicode( 10 ), )
    answer03_content = Column( Text )
    answer04 = Column( Unicode( 10 ), )
    answer04_content = Column( Text )
    answer05 = Column( Unicode( 10 ), )
    answer05_content = Column( Text )
    answer06 = Column( Unicode( 10 ), )
    answer06_content = Column( Text )
    answer07 = Column( Unicode( 10 ), )
    answer07_content = Column( Text )
    answer08 = Column( Unicode( 10 ), )
    answer08_content = Column( Text )
    answer09 = Column( Unicode( 10 ), )
    answer09_content = Column( Text )
    answer10 = Column( Unicode( 10 ), )
    answer10_content = Column( Text )



class Courseware( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_courseware'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    course_id = Column( Integer, ForeignKey( 'anbels_master_course.id' ) )
    course = relation( Course )
    desc = Column( Text )
    path = Column( Unicode( 5000 ) )
    url = Column( Unicode( 5000 ) )



class Game( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_game'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    course_id = Column( Integer, ForeignKey( 'anbels_master_course.id' ) )
    course = relation( Course )
    desc = Column( Text )
    path = Column( Unicode( 5000 ) )
    url = Column( Unicode( 5000 ) )



class School( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_school'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    location_id = Column( Integer )
    desc = Column( Text )


class_user_table = Table( 'anbels_logic_class_user', metadata,
    Column( 'user_id', Integer, ForeignKey( 'anbels_auth_user.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'class_id', Integer, ForeignKey( 'anbels_master_class.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'role', Unicode( 5 ), )  # T is teacher , S is sudent
 )




class Class( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_class'

    id = Column( Integer, autoincrement = True, primary_key = True )
    school_id = Column( Integer, ForeignKey( 'anbels_master_school.id' ) )
    school = relation( School )
    grade = Column( Integer, default = 1 )
    name = Column( Unicode( 1000 ), nullable = False )
    desc = Column( Text )
    users = relation( 'User', secondary = class_user_table, backref = 'class' )

#===============================================================================
# logic
#===============================================================================

class Plan( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_plan'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    school_id = Column( Integer, ForeignKey( 'anbels_master_school.id' ) )
    school = relation( School )
    desc = Column( Text )





class PlanCourse( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_plan_course'

    id = Column( Integer, autoincrement = True, primary_key = True )
    plan_id = Column( Integer, ForeignKey( 'anbels_logic_plan.id' ) )
    plan = relation( Plan )
    grade = Column( Integer, default = 1 )
    course_id = Column( Integer, ForeignKey( 'anbels_master_course.id' ) )
    course = relation( Course )
    desc = Column( Text )



class StudyLog( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_study_log'

    id = Column( Integer, autoincrement = True, primary_key = True )
    school_id = Column( Integer )
    class_id = Column( Integer )
    grade = Column( Integer )

    user_id = Column( Integer, ForeignKey( 'anbels_auth_user.id' ) )
    user = relation( User )
    type = Column( Unicode( 5 ), )  # G is game , C is courseware P is course
    refer_id = Column( Integer )
    refer_name = Column( Unicode( 1000 ) )
    start_time = Column( DateTime )
    complete_time = Column( DateTime )
    score = Column( Float )
    remark = Column( Text )


#===============================================================================
# system
#===============================================================================
class SystemLog( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_system_log'

    id = Column( Integer, autoincrement = True, primary_key = True )
    user_id = Column( Integer )
    remark = Column( Text )


class DataDictionary( DeclarativeBase ):
    __tablename__ = 'anbels_system_data_dictionary'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ) )
    value = Column( Text )


#===============================================================================
# for assign the system no value
#===============================================================================
class SystemNo( DeclarativeBase ):
    __tablename__ = 'anbels_system_no'

    system_no = Column( Integer, autoincrement = True, primary_key = True )
    active = Column( Integer, default = 0 )



class CurrentUser( DeclarativeBase ):
    __tablename__ = 'anbels_system_current_user'

    user_key = Column( Unicode( 50 ), primary_key = True )
    time_stamp = Column( DateTime, default = dt.now )



def init():
    print "drop all tables"
    metadata.drop_all( engine, checkfirst = True )
    print "create table"
    metadata.create_all( engine )
    print "insert default value"

    admin = User( system_no = '10000000', name = u'超级管理员' , gender = u'男', password = 'dicJp9R3v8xE2' )
    teacher = User( system_no = '10000001', name = u'老师甲', gender = '女', password = 'dirm.l/sEXGj2' )
    student = User( system_no = '10000002', name = u'学生甲', gender = u'男', password = 'diOP2PAYn8gE6' )

    rule01 = Rule( name = 'account_view', title = '账号管理', )
    rule02 = Rule( name = 'account_add', title = '账号增加', )
    rule03 = Rule( name = 'account_edit', title = '账号修改', )
    rule04 = Rule( name = 'account_del', title = '账号删除', )
    rule05 = Rule( name = 'account_exp', title = '账号导出', )
    rule06 = Rule( name = 'plan_view', title = '教学计划管理', )
    rule07 = Rule( name = 'plan_add', title = '教学计划增加', )
    rule08 = Rule( name = 'plan_edit', title = '教学计划修改', )
    rule09 = Rule( name = 'plan_del', title = '教学计划删除', )
    rule10 = Rule( name = 'question_view', title = '题库管理', )
    rule11 = Rule( name = 'question_add', title = '题库增加', )
    rule12 = Rule( name = 'question_edit', title = '题库修改', )
    rule13 = Rule( name = 'question_del', title = '题库删除', )
    rule14 = Rule( name = 'system_view', title = '系统管理', )
    rule15 = Rule( name = 'school_view', title = '学校管理', )
    rule16 = Rule( name = 'school_add', title = '学校添加', )
    rule17 = Rule( name = 'school_edit', title = '学校修改', )
    rule18 = Rule( name = 'school_del', title = '学校删除', )
    rule19 = Rule( name = 'class_view', title = '班级管理', )
    rule20 = Rule( name = 'class_add', title = '班级增加', )
    rule21 = Rule( name = 'class_edit', title = '班级修改', )
    rule22 = Rule( name = 'class_del', title = '班级删除', )
    rule23 = Rule( name = 'course_view', title = '课程管理', )
    rule24 = Rule( name = 'course_add', title = '课程增加', )
    rule25 = Rule( name = 'course_edit', title = '课程修改', )
    rule26 = Rule( name = 'course_del', title = '课程删除', )
    rule27 = Rule( name = 'courseware_view', title = '单个课件或者游戏管理', )
    rule28 = Rule( name = 'courseware_add', title = '课件或游戏增加', )
    rule29 = Rule( name = 'courseware_edit', title = '课件或游戏修改', )
    rule30 = Rule( name = 'courseware_del', title = '课件或游戏删除', )


    rule31 = Rule( name = 'account_view_all', title = '查看所有账号', )
    rule32 = Rule( name = 'plan_view_all', title = '查看所有教学计划', )
    rule33 = Rule( name = 'school_view_all', title = '查看所有学校', )
    rule34 = Rule( name = 'class_view_all', title = '查看所有班级', )
    rule35 = Rule( name = 'course_view_all', title = '查看所有课程', )
    rule36 = Rule( name = 'into_the_background', title = '进入后台管理', )

    DBSession.add_all( [ admin, teacher, student,
                        rule01, rule02, rule03, rule04, rule05, rule06, rule07, rule08, rule09,
                        rule10, rule11, rule12, rule13, rule14, rule15, rule16, rule17, rule18, rule19,
                        rule20, rule21, rule22, rule23, rule24, rule25, rule26, rule27, rule28, rule29,
                        rule30, rule31, rule32, rule33, rule34, rule35, rule36,
                        ] )
    DBSession.flush()
    gAdmin = Group( title = 'ADMIN' , display_name = '超级管理员' )
    gAdmin.users = [admin, ]
    gAdmin.rules = ",".join( map( str, [rule01.id, rule02.id, rule03.id, rule04.id, rule05.id, rule06.id, rule07.id, rule08.id, rule09.id,
                             rule10.id, rule11.id, rule12.id, rule13.id, rule14.id, rule15.id, rule16.id, rule17.id, rule18.id, rule19.id,
                             rule20.id, rule21.id, rule22.id, rule23.id, rule24.id, rule25.id, rule26.id, rule27.id, rule28.id, rule29.id,
                             rule30.id, rule31.id, rule32.id, rule33.id, rule34.id, rule35.id, rule36.id,
                             ] ) )
    gTeacher = Group( title = 'TEACHER' , display_name = '老师' )
    gTeacher.users = [teacher, ]
    gTeacher.rules = ",".join( map( str, [rule01.id, rule02.id, rule03.id, rule04.id, rule05.id, rule06.id, rule36.id,
                               ] ) )

    gStudent = Group( title = 'STUDENT' , display_name = '学生' )
    gStudent.users = [student, ]
    DBSession.add_all( [gAdmin, gTeacher, gStudent] )

    cr1 = Course( name = u'课程一', )
    cr2 = Course( name = u'课程二' )
    cr3 = Course( name = u'课程三' )
    cr4 = Course( name = u'课程四' )
    cr5 = Course( name = u'课程五' )
    DBSession.add_all( [cr1, cr2, cr3, cr4, cr5, ] )

    qs1 = Question( course = cr1, content = 'aa' , correct_answer = 'a' , answer01 = 'a' , answer01_content = '01' )
    qs2 = Question( course = cr2, content = 'bb' , correct_answer = 'c' , answer01 = '' , answer01_content = '01' )
    qs3 = Question( course = cr3, content = 'cc' , correct_answer = 'c' , answer01 = 'a' , answer01_content = '01' )
    qs4 = Question( course = cr4, content = 'dd' , correct_answer = 'd' , answer01 = 'a' , answer01_content = '01' )
    qs5 = Question( course = cr5, content = 'ee' , correct_answer = 'a' , answer01 = 'a' , answer01_content = '01' )
    qs6 = Question( course = cr1, content = 'ff' , correct_answer = 'b' , answer01 = 'a' , answer01_content = '01' )
    qs7 = Question( course = cr2, content = 'hh' , correct_answer = 'c' , answer01 = 'a' , answer01_content = '01' )
    qs8 = Question( course = cr3, content = 'gg' , correct_answer = 'a' , answer01 = 'a' , answer01_content = '01' )
    DBSession.add_all( [qs1, qs4, qs3, qs4, qs5, qs6, qs7, qs8 ] )

    cw1 = Courseware( name = u'课件一', course = cr1 )
    cw2 = Courseware( name = u'课件二', course = cr2 )
    cw3 = Courseware( name = u'课件三', course = cr3 )
    cw4 = Courseware( name = u'课件四', course = cr4 )

    cw10 = Courseware( name = u'课件一十', course = cr1 )
    cw20 = Courseware( name = u'课件二十', course = cr2 )
    cw30 = Courseware( name = u'课件三十', course = cr3 )
    cw40 = Courseware( name = u'课件四十', course = cr4 )

    DBSession.add_all( [cw1, cw2, cw3, cw4, cw10, cw20, cw30, cw40, ] )

    #===========================================================================
    # shool
    #===========================================================================

    school = School( name = u'罗湖小学', location_id = 2334 )
    school2 = School( name = u'红岭小学', location_id = 2334 )
    school3 = School( name = u'螺岭小学', location_id = 2334 )
    school4 = School( name = u'福田实验小学', location_id = 2335 )
    school5 = School( name = u'新洲小学', location_id = 2335 )
    DBSession.add_all( [school, school2, school3, school4, school5, ] )
    clz = Class( school = school, grade = 1, name = u'一年级一班' )
    clz2 = Class( school = school2, grade = 2, name = u'二年级二班' )
    clz3 = Class( school = school3, grade = 3, name = u'三年级三班' )
    clz4 = Class( school = school4, grade = 4, name = u'四年级四班' )
    DBSession.add_all( [clz, clz2, clz3, clz4, ] )
    clz.users = [student, teacher ]
    clz2.users = [teacher, ]
    clz3.users = [teacher, ]

    plan1 = Plan( name = u'罗湖小学教堂计划', school = school )
    pc1 = PlanCourse( plan = plan1, grade = 1, course = cr1 )

    plan2 = Plan( name = u'红岭小学教堂计划', school = school2 )
    pc2 = PlanCourse( plan = plan2, grade = 1, course = cr2 )

    DBSession.add_all( [plan1, plan2, pc1, pc2 ] )

    DBSession.commit()
    print "Done"


if __name__ == "__main__":
    init()
